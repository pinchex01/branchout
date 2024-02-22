<?php

namespace App\Http\Controllers\Agent;

use App\Models\Event;
use App\Models\Order;
use App\Models\Organiser;
use App\Models\Payment;
use App\Models\ReservedTicket;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Organiser $organiser, Request $request)
    {
        $orders  = $organiser->sales_orders()->paginate(20);

        return view('agent.orders.index',[
            'organiser' => $organiser,
            'orders' => $orders,
        ])->with('page_title',"Event Orders");
    }

    public function show(Organiser $organiser,Order $order ,Request $request)
    {
        $order->load(['order_items','order_items.ticket','attendees']);

        return view('agent.orders.view',[
            'organiser' => $organiser,
            'order' => $order,
        ])->with('page_title',"Event Order #{$order->id} Details");
    }

    public function buyTickets(Organiser $organiser, Event $event, Request $request)
    {
        $user = $request->user();
        $user->fresh();

        //initial primitive validation
        $this->validate($request,[
            'channel' => "required|in:pesaflow,points",
            'tickets' => "required|array"
        ]);

        //validate ticket availability and purchase order
        list($tickets, $errors) = Order::validate_tickets_purchase($request);
        if ($errors){
            return redirect()->back()
                ->withErrors($errors)
                ->withInput($request->all())
                ->with('alerts',array_get($errors,'alerts'));
        }


        /*
         * Remove any tickets the user has reserved
         */
        ReservedTicket::where('session_id', '=', session()->getId())->delete();

        $expires_at = Carbon::now()->addMinutes(15);

        //create reservation to prevent overbooking
        $reservations = Event::reserve_tickets($event, $user, $tickets, $expires_at);
        $total = collect($reservations)->sum('total');

        //clear previous session
        $this->clear_order_session($event,$request);

        session(['ticket_order_' . $event->id => [
            'tickets' => $tickets,
            'event' => $event,
            'quantities' => $request->input('tickets'),
            'checkout' => null,
            'expires_at' => $expires_at,
            'reservations' => $reservations,
            'total' => $total
        ]]);

        return redirect()->route('agent.events.confirm_order', [$organiser->slug, $event->id]);
    }

    public function confirmOrderDetails(Organiser $organiser, Event $event, Request $request)
    {
        $order_info = $this->checkout_data($event);
        if (!$order_info)
            return redirect()->route('agent.events.view', [$organiser->slug, $event->id]);

        if ($request->method() == 'GET'){
            //dd($order_info);
            return view('agent.orders.purchase', $order_info + ['user' => user(), 'organiser' => $organiser])
                ->with('page_title', "Complete Order Details");
        }

        $user = user();

        $this->validate($request,[
            'attendee.*.first_name' => "required",
            'attendee.*.last_name' => "required",
            'attendee.*.email' => "required|email",
            'attendee.*.phone' => "required|full_phone",
            'attendee.*.ticket_id' => "required|exists:tickets,id",
            'sales_person_code' => [
                "nullable",
                Rule::exists('sales_people','code')
                    ->where('event_id', $event->id)
                    ->where('status','active')
            ]
        ]);

        //try and create a payment request
        $payment_data  = [
            'currency' => 'KES',
            'fee' => 0,
            'amount' => $order_info['total'],
            'total' => $order_info['total'],
            'notes' => "order for {$event}",
            'payload' => [
                'event_id' => $event->id,
                'tickets' => $order_info['reservations'],
                'user_id' => $user->id,
                'amount' => $order_info['total'],
                'attendees' => $request->input('attendees')
            ]
        ];

        //create a payment entry with order payload
        $payment = Payment::create_payment('MPESA', $payment_data, $user);
        $payment->payment_code = $request->input('sales_person_code');
        $payment->save();

        //check if order is free and complete purchase
        if(!$payment->total){
            list ($x, $order) = Payment::complete_payment($payment);

            $this->clear_order_session($event, $request);

            if ($order) {
                return redirect()->route('agent.orders.view', [$organiser->slug, $order->id])
                    ->with('alerts', [
                        ['type' => 'success', 'message' => "Your order has been processed"]
                    ]);
            } else {
                return redirect()->back()
                    ->with('alerts', [
                        ['type' => 'danger', 'message' => "Something went wrong. Your order could not be processed, please try again."]
                    ]);

            }
        }
        //update session data with the payment info
        $order_info['payment'] = $payment;
        $order_info['callBackURLOnSuccess'] = route('agent.orders.index', ['success' => 'true']);
        $order_info['callBackURLOnFail'] = route('agent.orders.index', ['success' => 'failed']);
        session([ 'ticket_order_' . $event->id => $order_info]);

        return redirect()->route('app.events.checkout', [$event->slug]);
    }


    private function clear_order_session(Event $event, Request $request)
    {
        $request->session()->forget('ticket_order_' . $event->id );
    }
}
