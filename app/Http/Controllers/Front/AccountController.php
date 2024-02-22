<?php

namespace App\Http\Controllers\Front;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Knp\Snappy\Image;
use Knp\Snappy\Pdf;
use ZipArchive;

class AccountController extends Controller
{
    public function dashboard()
    {
        $user = user();
        $subscribed_events = Event::selectRaw("distinct(events.id), events.name as title, events.slug, 
        events.start_date as start, events.end_date as end, events.location, organisers.name as organiser")
            ->join('orders', 'orders.event_id', '=', 'events.id')
            ->join('organisers', 'organisers.id', '=', 'events.organiser_id')
            ->where('orders.user_id', $user->id)
            ->whereIn('orders.status', ['paid'])
            ->latest('events.start_date')
            ->get();

        $events = Event::query()
            ->with(['organiser', 'tickets'])
            ->status('published')
            ->latest('events.created_at')
            ->take(5)
            ->get();

        $orders = $user->orders()->latest('orders.created_at')->take(5)->get();
        $tickets = Attendee::query()
            ->selectRaw("attendees.id as id, attendees.ref_no, attendees.first_name, attendees.last_name, orders.ref_no as order_no, events.name as event_name, tickets.name as ticket_name, tickets.price as price")
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->join('events', 'events.id', '=', 'attendees.event_id')
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where('orders.user_id', $user->id)
            ->whereIn('orders.status', ['paid'])
            ->latest('orders.created_at')
            ->take(10)
            ->get();

        $tickets_sold = Attendee::query()
            ->whereHas('order', function ($query) use ($user) {
                return $query->whereIn('orders.status', ['paid', 'complete'])
                    ->where('orders.user_id', $user->id);
            })->count();
        $total_spent = $user->orders()->whereIn('orders.status', ['paid', 'complete'])->sum('amount');

        //add url to my events
        $my_events = array_map(function ($event) {
            return $event + ['url' => route('app.events.view', $event['slug'])];
        }, $subscribed_events->toArray());

        \JavaScript::put([
            'MyEvents' => $my_events
        ]);

        return view('user.dashboard', [
            'events' => $events,
            'orders' => $orders,
            'my_events' => $subscribed_events,
            'tickets' => $tickets,
            'summary' => collect([
                'points' => $user->points,
                'total_tickets' => $tickets_sold,
                'spent' => $total_spent
            ])
        ])->with('page_title', 'Dashboard');
    }

    public function listEvents(Request $request)
    {
        $user = user();
        $events = Event::selectRaw("distinct(events.id), events.name as title, events.slug, 
        events.start_date as start, events.end_date as end, events.location, organisers.name as organiser")
            ->join('orders', 'orders.event_id', '=', 'events.id')
            ->join('organisers', 'organisers.id', '=', 'events.organiser_id')
            ->where('orders.user_id', $user->id)
            ->whereIn('orders.status', ['paid'])
            ->latest('events.start_date')
            ->get();

        return view('user.events.index', [
            'events' => $events,
        ])->with('page_title', 'Upcoming Events');
    }

    public function listOrders(Request $request)
    {
        $user = user();

        $orders = $user->orders()
            ->complete()
            ->latest("orders.created_at")
            ->paginate(20);

        $data = [
            'orders' => $orders
        ];
        return view('user.orders.index', $data)
            ->with('page_title', "My Orders");
    }

    public function viewOrder(Order $order, Request $request)
    {
        $this->before(function () use ($order) {
            return $order->user_id == user()->id;
        }, 404);

        $data = [
            'order' => $order
        ];
        return view('user.orders.view', $data)
            ->with('page_title', "My Orders");
    }

    public function listTickets(Request $request)
    {
        $user = user();
        $tickets = Attendee::query()
            ->selectRaw("attendees.id as id, attendees.ref_no, attendees.first_name, attendees.last_name, orders.ref_no as order_no, events.name as event_name, tickets.name as ticket_name, tickets.price as price")
            ->join('orders', 'orders.id', '=', 'attendees.order_id')
            ->join('events', 'events.id', '=', 'attendees.event_id')
            ->join('tickets', 'tickets.id', '=', 'attendees.ticket_id')
            ->where('orders.user_id', $user->id)
            ->whereIn('orders.status', ['paid'])
            ->where(function ($query) use ($request) {
                if ($ticket_no = $request->input('filters.ticket_no')) {
                    return $query->where('attendees.ref_no', $ticket_no);
                }

                return $query;
            })
            ->latest('attendees.created_at')
            ->paginate(20);

        $data = [
            'tickets' => $tickets
        ];

        return view('user.tickets.index', $data)
            ->with("page_title", "My Tickets");
    }

    public function downloadTicket(Attendee $attendee, Request $request)
    {
        $this->before(function () use ($attendee) {
            $order = $attendee->order;
            return $order->user_id == user()->id && in_array($order->status, ['paid', 'complete']);
        });

        $attendee->generate_ticket();
        $path = storage_path($attendee->get_ticket_path());

        return response()->download($path);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTickets(Request $request)
    {
        $this->validate($request, [
            'tickets' => "required|array"
        ]);
        $files = [];
        Attendee::whereIn('ref_no', $request->input('tickets'))
            ->each(function ($ticket) use (&$files) {
                $ticket->generate_ticket();
                $files[$ticket->ref_no] = storage_path($ticket->get_ticket_path());
            });

        $zipname = "tickets-".time().".zip";
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($files as $name => $file) {
            $zip->addFile($file, $name.".pdf");
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);

        //return response()->download(public_path($zipname));
    }

    public function editTicket(Attendee $attendee, Request $request)
    {
        $this->before(function () use ($attendee) {
            $order = $attendee->order;
            return $order->user_id == user()->id && in_array($order->status, ['paid', 'complete']) && !$attendee->check_in_time;
        });

        $this->validate($request, [
            'first_name' => "required|alpha",
            'last_name' => "required|alpha",
            'phone' => "nullable|full_phone",
            'email' => "nullable|email"
        ]);

        $attendee->fill($request->only(['first_name', 'last_name', 'phone', 'email']));
        $attendee->save();

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Your ticket details has been edited successfully!"]
            ]);
    }

    public function viewTicket(Attendee $attendee, Request $request)
    {
        $attendee->load(['order', 'event', 'ticket']);

        return view('pdfs.ticket', [
            'order' => $attendee->order,
            'event' => $attendee->event,
            'ticket' => $attendee->ticket,
            'attendee' => $attendee
        ]);
    }

    public function myMoney(Request $request)
    {
        $user = $request->user();

        $sales = $user->events()->with(['event'])->latest('sales_people.created_at')->paginate(20);

        $user->events()
            ->selectRaw("sum(fees) as fees, settled")
            ->groupBy('settled')
            ->get()
            ->each(function ($sale) use (&$summary) {
                if ($sale->settled)
                    $summary['settled'] = $sale->fees;

                $summary['pending'] = $sale->fees;
            });

        $data = [
            'sales' => $sales,
            'summary' => collect($summary)
        ];

        return view('user.sales.index', $data)
            ->with('page_title', "My Money");
    }

}
