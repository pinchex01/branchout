<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organiser;
use App\Models\Payment;
use App\Models\ReservedTicket;
use App\Models\SalesPerson;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected $tickets_for_purchase = null;

    public function landing(Request $request)
    {
        $events = Event::query()
            ->nameLike($request->get('q'))
            ->with(['organiser','tickets' => function($query){
              $query->whereRaw("DATE(NOW()) <= DATE(tickets.end_sale_date)");
            }])
            ->status('published')
            ->latest('events.created_at')
            ->take(5)
            ->get();


        $data = [
            'events' => $events
        ];

        $this->flashInput($request->all());
        return view('pages.default', $data)
            ->with("page_title", "Event Manager");
    }

    public function allEvents(Request $request)
    {
        $events = Event::query()
            ->nameLike($request->get('q'))
            ->with(['organiser','tickets' => function($query){
              $query->whereRaw("DATE(NOW()) <= DATE(tickets.end_sale_date)");
            }])
            ->status('published')
            ->latest('events.created_at')
            ->paginate(20);


        $data = [
            'events' => $events
        ];

        $this->flashInput($request->all());
        return view('pages.events', $data)
            ->with("page_title", "Event Manager");
    }

    public function viewEvent(Event $event, Request $request)
    {
        $event->load([ 'organiser','tickets' => function($query){
          $query->whereRaw("DATE(NOW()) <= DATE(tickets.end_sale_date)");
        }]);
        $agent  =  null;
        if($request->get('agent')){
            $agent = Organiser::find($request->get('agent'));
        }

        $comments  = $event->comments()->with(['author'])->latest('comments.created_at')->get();
        \JavaScript::put([
            'Tickets' => $event->tickets,
            'PaymentConfigs' => config('pesaflow'),
            'Comments' => $comments
        ]);

        $data = [
            'event' => $event,
            'organiser' => $agent,
            'comments' => $comments
        ];

        return view('pages.view_event', $data)
            ->with('page_title', "Event Details");
    }

    public function viewOrganiser(Organiser $organiser, Request $request)
    {
        $organiser->load(['events']);

        $data = [
            'organiser' => $organiser
        ];

        return view('pages.view_organiser', $data)
            ->with('page_title', "Event Details");
    }

    public function buyTickets(Event $event, Request $request)
    {
        $this->clear_order_session($event, $request);

        $this->validate($request, [
            'tickets' => "required|array",
            'sales_agent_code' => [
                "nullable",
                Rule::exists('sales_people','code')
                    ->where('event_id',$event->id)
                    ->where('status','active')
            ]
        ],[
            "sales_agent_code.*" => "Sales agent number entered is invalid"
        ]);


        //validate ticket availability and purchase order
        list($tickets, $errors) = Order::validate_tickets_purchase($request);
        if ($errors){
            \JavaScript::put([
                'errors' => $errors
            ]);

            return redirect()->back()
                ->withErrors($errors)
                ->withInput($request->all())
                ->with('alerts',array_get($errors,'alerts'));
        }

        $cart_id = $request->session()->getId();
        $user = $request->user();

        /*
         * Remove any tickets the user has reserved
         */
        ReservedTicket::cancel_reservation($cart_id);

        $expires_at = Carbon::now()->addMinutes(15);

        //create reservation to prevent overbooking
        $reservations = Event::reserve_tickets($cart_id, $event, $tickets, $expires_at, $user);
        $total = collect($reservations)->sum('total');

        //clear previous session
        $this->clear_order_session($event,$request);

        $order_info  = [
            'cart_id' => $cart_id,
            'tickets' => $tickets,
            'event' => $event,
            'quantities' => $request->input('tickets'),
            'checkout' => null,
            'expires_at' => $expires_at,
            'reservations' => $reservations,
            'total' => $total,
            'sales_agent_code' => $request->input('sales_agent_code')
        ];
        session(['ticket_order_' . $event->id => $order_info]);

        return redirect()->route('app.events.confirm_order', [$event->slug]);
    }

    public function confirmOrderDetails(Event $event, Request $request)
    {
        $order_info = $this->checkout_data($event);
        if (!$order_info)
            return redirect()->route('app.events.view', $event->slug);

        if ($request->method() == 'GET'){
            \JavaScript::put([
                'OrderInfo' => $order_info
            ]);
            return view('pages.order_details', $order_info + ['user' => user()])
                ->with('page_title', "Complete Order Details");
        }

        $user = user();
        if($user){
            $this->validate($request,[
                'attendee.*.first_name' => "required",
                'attendee.*.last_name' => "required",
                'attendee.*.ticket_id' => "required|exists:tickets,id"
            ]);

        }else{
            $this->validate($request,[
                'first_name' => "required",
                'last_name' => "required",
                'email' => "required|email|unique:users,email",
                'phone' => "required|full_phone|unique:users,phone",

                /*
                 * Validate attendees
                 */
                'attendee.*.first_name' => "required",
                'attendee.*.last_name' => "required",
                'attendee.*.ticket_id' => "required|exists:tickets,id"
            ]);
            $user = User::getOrCreate(encode_phone_number($request->input('phone')), $request->only(['first_name','last_name','email','phone']), true);
        }

        //create order
        $attendees = $request->input('attendee');
        $sales_agent = SalesPerson::get_by_code($order_info['sales_agent_code']);
        $order =  ReservedTicket::create_order_from_reservation($order_info['cart_id'],$event,$user, $attendees,
            $sales_agent);

        if(!$order){
            return redirect()->route('app.events.view', $event->slug);
        }

        $this->clear_order_session($order->event, $request);

        if (!$order->amount){
            Order::complete_order($order,0);

            return redirect()->route('app.orders.complete', $order->id)
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Your order has been processed"]
                ]);
        }else{
            return redirect()->route('app.orders.checkout', [$order->id]);
        }
    }

    public function showCheckout(Order $order, Request $request)
    {
        if($order->is_complete())
            return redirect()->route('app.orders.view', [$order->id]);
        
        $event = $order->event;

        $user = user() ? : $order->user;

        \JavaScript::put([
            'Order' => $order->load(['order_items','order_items.ticket']),
            'frmCheckout' => $order->get_pesaflow_checkout_data()
        ]);

        return view('pages.checkout', [
            'event' => $event,
            'user' => $user,
            'action' => $action=null,
            'order' => $order,
        ])->with('page_title', "Complete Ticket Purchase");
    }

    /**
     * @param $payment_key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function completePayment($payment_key, Request $request)
    {
        $payment  = Payment::where('payment_key', $payment_key)
            ->whereNotIn('status',['complete'])
            ->firstOrFail();

        return redirect()->route('app.orders.list')
            ->with('alerts', [
                ['type' => 'success', 'message' => "Your payment has been received and is being processed"]
            ]);
    }

    public function checkoutSuccessful(Order $order, Request $request)
    {
        $order_info = $this->checkout_data($order->event);
        $this->clear_order_session($order->event, $request);

        return view('pages.order_complete', [
            'order' => $order
        ])->with('page_title', "Order Complete");
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return View
     */
    public function orderDetails(Order $order, Request $request)
    {
        return view('pages.order_view', [
            'order' => $order
        ])->with('Order Information');
    }

    private function clear_order_session(Event $event, Request $request)
    {
        $request->session()->forget('ticket_order_' . $event->id );
    }
}
