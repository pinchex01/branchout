<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderCancelled;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $events  =  Event::all();

        $orders  = Order::filter($request)
            ->with(['event','user'])
            ->latest('orders.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        $tickets  = Ticket::query()->get();

        \JavaScript::put([
            'Tickets' => $tickets,
            'Events' => $events
        ]);

        return view('admin.orders.index',[
            'orders' => $orders,
            'events' => $events->pluck('name', 'id')->toArray()
        ])->with('page_title',"Event Orders");
    }

    public function show(Order $order ,Request $request)
    {
        $order->load(['order_items','order_items.ticket','attendees']);

        return view('admin.orders.view',[
            'order' => $order,
        ])->with('page_title',"Event Order #{$order->id} Details");
    }

    public function previewTicket(Order $order, Request $request)
    {
        $event  = $order->event;
        $organiser = $event->organiser;
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

    public function sendNotification( Order $order, Request $request)
    {
        $order->generate_tickets(true);
        $order->send_order_processed_notification();

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Order # {$order->id} notification has been sent"]
            ]);
    }

    public function markAsPaid(Order $order, Request $request)
    {
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

    public function cancelOrder(Order $order, Request $request)
    {
        if(!$order->is_complete()){
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "You cannot cancel and order that has not been paid for"]
                ]);
        }
        $refund  = $request->input('refund') == 1;

        if(Order::cancel($order, $refund)){
            //reload to esnure changes reflect
            $order  = $order->fresh();
            event(new OrderCancelled($order));

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Order # {$order->ref_no} has successfully been cancelled."]
                ]);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'danger', 'message' => "It appears something went wrong"]
            ]);
    }

}
