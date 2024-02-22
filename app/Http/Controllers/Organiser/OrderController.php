<?php

namespace App\Http\Controllers\Organiser;

use App\Models\Event;
use App\Models\Order;
use App\Models\Organiser;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Organiser $organiser, Event $event, Request $request)
    {
        $this->can('view-orders');
        $orders  = $event->orders()->latest('orders.created_at')->paginate(20);
        $orders = Order::filter($request)
            ->with(['event','user'])
            ->where('event_id', $event->id)
            ->latest('orders.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        #todo: only fetch tickets that are still on sale
        $tickets  = $event->tickets()->get();

        \JavaScript::put([
            'Tickets' => $tickets
        ]);

        return view('organiser.orders.index',[
            'organiser' => $organiser,
            'event' => $event,
            'orders' => $orders,
            'tickets' => $tickets
        ])->with('page_title',"Event Orders");
    }

    public function show(Organiser $organiser, Event $event, Order $order ,Request $request)
    {
        $this->can('view-orders');
        $order->load(['order_items','order_items.ticket','attendees']);

        return view('organiser.orders.view',[
            'organiser' => $organiser,
            'event' => $event,
            'order' => $order,
        ])->with('page_title',"Event Order #{$order->id} Details");
    }

    public function previewTicket(Organiser $organiser, Event $event, Order $order, Request $request)
    {
        $data = [
            'order'     => $order,
            'event'     => $event,
            'attendees' => $order->attendees,
            'css'       => file_get_contents(public_path('css/ticket.css')),
            'image'     => base64_encode(file_get_contents($organiser->full_avatar_path)),
            'organiser' => $organiser
        ];
        return view('pdfs.ticket_pdf', $data)->render();
    }

    public function markAsPaid(Organiser $organiser, Event $event, Order $order, Request $request)
    {
        $this->can('create-payments');
        //todo: ensure whoever is doing this has the permission and owns the order

        #only generate payment when order is not already marked as paid
        if($order->status  != 'paid'){
            $payment = $order->create_payment($request->input('channel','system'), $order->amount);
            Payment::complete_payment($payment, $order->user);

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Order is being updated!"]
                ]);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'danger', 'message' => "Order # {$order->id} has already been paid"]
            ]);
    }

    public function sendNotification(Organiser $organiser, Event $event, Order $order, Request $request)
    {
        $this->can('view-orders');
        $order->generate_tickets(true);
        $order->send_order_processed_notification();

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Order # {$order->id} notification has been sent"]
            ]);
    }
}
