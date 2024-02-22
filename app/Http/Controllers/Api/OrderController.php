<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderCompleted;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ReservedTicket;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function addAttendee(Order $order, Request $request)
    {
        $order->load(['event','tickets','order_items']);

        $this->validate($request,[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' =>'nullable|email',
            'phone' => 'nullable|phone',
            'order_item_id'=> 'required|exists:order_items,id',
        ]);

        $order_item = Order::find($request->input('order_item_id'));

        //get no of attendees assigned to the ticket
        $attendees = Attendee::where([
            'order_id' => $order->id,
            'ticket_id' => $order_item->ticket_id
        ])->whereIn('status',['pending','arrived'])->count();

        //check if tickets have been exhausted
        if ($attendees >= $order_item->quantity)
            return response()->json([
                'alerts' => ["You have already assigned maximum number of attendees for this ticket"]
            ], 422);

        $data = $request->all();
        $data['event_id'] = $order->event_id;
        $data['ticket_id'] = $order_item->ticket_id;
        $data['order_id'] = $order->id;
        $attendee = Attendee::add_attendee($data);

        return response()->json($attendee,200);
    }

    public function getTicketInfo(Request $request, $ticket_no)
    {
        $ticket = Attendee::get_by_ticket_no($ticket_no);

        if(!$ticket){
            return response()->json([
                'status' => 'fail',
                "message" => 'Ticket does not exist'
            ], 422);
        }

        $ticket->load(['event', 'order', 'ticket']);

        $data  = [
            'ticket_no' => $ticket_no,
            'first_name' => $ticket->first_name,
            'last_name' =>  $ticket->last_name,
            'full_name' => $ticket->first_name." ".$ticket->last_name,
            'email' => $ticket->email,
            'phone' => $ticket->phone,
            'check_in_time' => $ticket->check_in_time,
            'event' => $ticket->event,
            'order' => $ticket->order
        ];

        return response()->json($data, 200);
    }

    public function checkInTicket(Request $request)
    {
        $this->validate($request, [
           'ticket_no' => "required|exists:attendees,ref_no"
        ]);

        $ticket_no = $request->get('ticket_no');

        $ticket = Attendee::get_by_ticket_no($ticket_no);
        $ticket->load(['event', 'order', 'ticket']);

        if($ticket->check_in_time){
            return response()->json([
                'status' => 'fail',
                "message" => 'Ticket has already been checked in'
            ], 422);
        }

        $ticket = $ticket->check_in($request->user());

        $data  = [
            'ticket_no' => $ticket_no,
            'first_name' => $ticket->first_name,
            'last_name' =>  $ticket->last_name,
            'full_name' => $ticket->first_name." ".$ticket->last_name,
            'email' => $ticket->email,
            'phone' => $ticket->phone,
            'check_in_time' => $ticket->check_in_time,
            'event' => $ticket->event,
            'order' => $ticket->order
        ];

        return response()->json($data, 200);
    }

    /*
     * ------------------------------------------------------------------
     * Purchase ticket APIs
     * -----------------------------------------------------------------
     */
    public function buyTickets(Request $request)
    {
        $this->validate($request, [
            'event_id' => "required|exists:events,id",
            'tickets' => "required|array",
            'cart_id' => "required",
            'sales_agent_code' => [
                "nullable",
                Rule::exists('sales_people','code')
                    ->where('event_id',$request->input('event_id'))
                    ->where('status','active')
            ]
        ]);




        $cart_id = $request->input('cart_id');
        $event = Event::findOrFail($request->input('event_id'));
        $user = $request->user();

        list($tickets, $errors) = Order::validate_tickets_purchase($request);
        if($errors){
            return response()->json($errors,422);
        }

        /*
         * Remove any tickets the user has reserved
         */
        ReservedTicket::cancel_reservation($cart_id);

        $expires_at = Carbon::now()->addMinutes(15);

        //create reservation to prevent overbooking
        $reservations = Event::reserve_tickets($cart_id, $event, $tickets, $expires_at, $user);
        $total = collect($reservations)->sum('total');


        $data  = [
            'cart_id' => $cart_id,
            'tickets' => $tickets,
            'event' => $event,
            'checkout' => null,
            'expires_at' => $expires_at,
            'reservations' => $reservations,
            'total' => $total
        ];

        return response()->json($data, 200);
    }


    private function validate_order_info(Request $request)
    {
        $this->validate($request,[
            'cart_id' => "required|exists:reserved_tickets,session_id",
            'payment_channel' => "required|in:mpesa,points,wallet",
            'event_id' => "required|exists:events,id",
            'first_name' => "required",
            'last_name' => "required",
            'email' => "required|email",
            'phone' => "required|full_phone",
            'id_number' => "required",

            /*
             * Validate attendees
             */
            'attendee.*.first_name' => "required",
            'attendee.*.last_name' => "required",
            'attendee.*.ticket_id' => "required|exists:tickets,id",
            'sales_agent_code' => [
                "nullable",
                Rule::exists('sales_people','code')
                    ->where('event_id',$request->input('event_id'))
                    ->where('status','active')
            ]
        ]);

    }

    private function check_if_user_can_buy_with_points_or_wallet($payable, Request $request, $user)
    {
        $payment_error  = null;

        if(in_array($request->input('payment_channel'),['wallet','points'])){
            $payment_channel  = $request->input('payment_channel');
            if (!$user){
                $payment_error = "You can only use Wallet or Points when logged in";
            }else{
                if($user->points < $payable && $payment_channel == 'points' )
                    $payment_error  = "You have insufficient points";

                if($user->account->balance < $payable && $payment_channel == 'wallet')
                    $payment_error  = "You have insufficient funds in your wallet to complete this transaction";
            }
        }

        return $payment_error;
    }

    public function confirmOrder(Request $request)
    {
        $this->validate_order_info($request);

        $user = User::getOrCreate($request->input('id_number'), $request->only(['first_name','last_name','email','phone']));
        if(!$user){
            return response()->json([
                'status' => 'fail',
                'message' => "Invalid id number or first name combination"
            ], 430);
        }

        $cart_id = $request->input('cart_id');
        $event = Event::findOrFail($request->input('event_id'));


        $reservation  = ReservedTicket::get_reservations($cart_id);

        //ensure wallet and points can only be used when the user is logged in
        $payment_error  = $this->check_if_user_can_buy_with_points_or_wallet($reservation['total'], $request, $user);

        //if there is any payment error stop and give feedback
        if($payment_error){
            return response()->json([
                'status' => 'fail',
                'message' => $payment_error
            ], 430);
        }

        //try and create a payment request
        $payment_data  = [
            'currency' => 'KES',
            'fee' => 0,
            'amount' => $reservation['total'],
            'total' => $reservation['total'],
            'notes' => "order for {$event}",
            'payload' => [
                'event_id' => $event->id,
                'tickets' => $reservation['items'],
                'user_id' => $user? $user->id : null,
                'amount' => $reservation['total'],
                'attendees' => $request->input('attendee'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'sales_agent_code' => $request->input('sales_agent_code')
            ]
        ];

        //create a payment entry with order payload
        $payment = Payment::create_payment($request->input('payment_channel'), $payment_data, $user);
        $payment->payment_code = $request->input('sales_person_code');
        if($user->id){
            $payment->user_id = $user->id;
        }
        $payment->save();

        //check if order is free and complete purchase
        if(!$payment->total){
            list ($x, $order) = Payment::complete_payment($payment);

            ReservedTicket::cancel_reservation($cart_id);
        }


        return response()->json($payment, 200);
    }

    public function getCheckoutPayload(Request $request)
    {
        $this->validate($request, [
            'payment_id' => "required|exists:payments,id"
        ]);

        $payment = Payment::findOrFail($request->input('payment_id'));
        if(in_array($payment->status, ['complete','processing'])){
            return response([
                'status' => 'fail',
                'message' => "Payment for this order has already been received"
            ], 430);
        }

        //ensure that pesaflow bill has been created else try to create the bill
        if (!$payment->payment_ref) {
            $ref  = Payment::get_pesaflow_bill_ref($payment);
            if(!$ref){
                return response([
                    'status' => 'fail',
                    'message' => "Sorry! An error occurred while trying to complete your payment"
                ], 430);
            }
            $payment->fresh();
        }

        //get all pesaflow configs
        $config = config('pesaflow');
        $signature = $payment->get_payment_signature();

        return view('partials.checkout_iframe', [
            'user' => new User(),
            'config' => $config,
            'payment' => $payment,
            'signature' => $signature,
            'callBackURLOnSuccess' => route('app.orders.list',['payment' => $payment->id,'success'=>'true']),
            'callBackURLOnFail' => route('app.orders.list',['payment' => $payment->id,'success'=>'fail'])
        ])->with('page_title', "Complete Ticket Purchase");
    }

    public function validateTicketPurchase(Request $request)
    {
        #log payment request
        \Log::info("Payment request", [$request->all()]);

        $plog = log_mpesa_payment_request($request);
        $validator  = \Validator::make($request->all(), [
            'amount' => "required",
            'id_number' => "required",
            'phone' => "required|full_phone"
        ]);
        
        if ($validator->fails()){
          $plog->update(['status' => 'rejected']);

            return response()->json([
                'status' => 'fail',
                'code' => 'BT000',
                'message' => "Required input missing",
                'errors' => $validator->errors()
            ], 430);

        }

        $amount = $request->input('amount');
        $ref_no = $request->input('id_number');
        $phone = $request->input('phone');

        #check if bill is for bar
        if(strtoupper($ref_no)  == 'BAR'){
            $plog->update(['status' => 'confirmed', 'notes' => "Validation success"]);
            
            
            return response([
                "status" => "ok",
                "message" => "Great"
            ], 200);
        }
        $ref = get_purchasable_from_ref_no($phone, [ 'phone' => $phone, 'username' => $ref_no]);
        if(!$ref){
            $plog->update(['status' => 'rejected', 'notes' => "No order or user found"]);

            return response([
                "status" => "fail",
                "code" => "BT0002",
                "message" => "No order or user found for reference no {$ref_no}"
            ], 430);
        }

        list($type, $what) = $ref;
        if ($type == 'user'){
            #check if user was create else fail
            if(!$what){
              $plog->update(['status' => 'rejected', 'notes' => "Phone number already in use"]);

              return response([
                  "status" => "fail",
                  "code" => "BT0009",
                  "message" => "Phone number already in use"
              ], 430);
            }

            $ticket  = Ticket::query()->onSale()->wherePrice($amount)->first();
            if (!$ticket){

              $plog->update(['status' => 'rejected', 'notes' => "No tickets found for price"]);

                return response([
                    "status" => "fail",
                    "code" => "BT0001",
                    "message" => "No ticket is on sale for that amount"
                ], 430);
            }
        }else{
          #ensure amount is exact
            if($what->amount  != $amount){

              $plog->update(['status' => 'rejected', 'notes' => "Invalid amount, expected {$what->amount} got {$amount}"]);

                return response([
                    "status" => "fail",
                    "code" => "BT0001",
                    "message" => "The amount is less or greater than the order total. Please pay exact amount"
                ], 430);
            }

            #ensure order does not contain any expired tickets
            $expired_tickets  =  $what->has_expired_tickets();
            if ($expired_tickets){
              $plog->update(['status' => 'rejected', 'notes' => "Order contains expired tickets"]);

                return response([
                    "status" => "fail",
                    "code" => "BT0007",
                    "message" => "Order contains ticket(s) that have already exipred."
                ], 430);
            }
        }

        $plog->update(['status' => 'confirmed', 'notes' => "Validation success"]);
        return response([
            "status" => "ok",
            "message" => "Great"
        ], 200);

    }

    public function createFromPayment(Request $request)
    {
        $validator  = \Validator::make($request->all(), [
            'amount' => "required",
            'id_number' => "required",
            'phone' => "required|full_phone"
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'code' => 'BT000',
                'message' => "Required input missing",
                'errors' => $validator->errors()
            ], 430);
        }

        $amount = $request->input('amount');
        $ref_no = $request->input('id_number');
        $phone  = $request->input('phone');
        $code  = $request->input('trx_ref');

        $order  = null;
        $payment = null;

        #check if bill is for bar
        if(strtoupper($ref_no) == 'BAR'){
            $bill = \App\Models\Bill::create_bill($code, $ref_no, $amount,$phone);
            
            if ($bill){
                return response()->json($bill, 200);
            }
           
           return response()->json([
                'status' => 'fail',
                'code' => 'BT003',
                'message' => "Internal error, could not complete your request"
            ], 430);
        }

        # get or create user from id and phone
        if ($ref = get_purchasable_from_ref_no($phone, [ 'phone' => $phone, 'username' => $ref_no])){
            list($type, $what) = $ref;

            if ($type == 'user'){
                $ticket  = Ticket::query()->onSale()->wherePrice($amount)->first();

                #no ticket is on sale for that amout
                if(!$ticket)
                    return null;

                #first log payment
                $payment = $this->create_payment_from_request('mpesa', $code, $ref_no, $amount,$ticket->event,$what);
                $order = $this->create_order_from_request($request, $ticket, $what);
            }else{
                $order = $what;
                #first log payment
                $payment = $this->create_payment_from_request('mpesa', $code, $ref_no, $amount,$order->event,$order->user);
                $payment->update(['order_id' => $order->id]);
            }
        }
        $payment->update(['order_id' => $order->id]);

        #finaly process payment
        Payment::complete_payment($payment,$order->user);

        if($order){
            return response()->json($order);
        }

        return response()->json([
            'status' => 'fail',
            'code' => 'BT003',
            'message' => "Internal error, could not complete your request"
        ], 430);
    }

    private function create_order_from_request(Request $request, Ticket $ticket, User $user)
    {
        $amount = $request->input('amount');

        $order  = Order::create_order($ticket->event_id, $user, $amount, [
            [
                "ticket_id" => $ticket->id,
                "event_id" => $ticket->event_id,
                "quantity" => 1,
                'ticket' => $ticket->toArray()
            ]
        ], "1 {$ticket->name} ticket for {$ticket->event} @ {$ticket->price}");

        #add attendee information
        $order->add_attendees([
            [
                'ticket_id' => $ticket->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name
            ]
        ]);

        return $order->fresh(['attendees','payments']);
    }

    public function createOrderManual(Request $request)
    {
        $this->validate($request, [
            'event_id' => "required|exists:events,id",
            'ticket_id' => "required|exists:tickets,id",
            'id_number' => "required",
            'email' => "nullable|email",
            "phone" => "nullable|full_phone",
            'payment' => "required|in:full,discounted,free",
            'discount' => "nullable|required_if:payment,discounted",
            'attendee.*.first_name' => "required",
            'attendee.*.last_name' => "required",
        ]);

        #todo: check that ticket and even match
        $event = Event::find($request->input('event_id'));
        $ticket = Ticket::find($request->input('ticket_id'));
        $payment  = $request->input('payment');

        #first create user
        $user  =  User::getOrCreate($request->input('id_number'), [
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
        ]);

        

        $attendees = $request->input('attendees');
        $quantity  = count($attendees);
        $price  =  $ticket->price  * $quantity;
        if($payment == 'free') $price  = 0;
        if($payment == 'discounted') $price = $request->input('discount', $price);

        #create order
        $order = Order::create_order($event->id,$user, $price, [
            [
                "ticket_id" => $ticket->id,
                "event_id" => $event->id,
                "quantity" => $quantity,
                'ticket' => $ticket->toArray(),
                'price' => $price
            ]
        ], "Order for {$event}, Ticket {$ticket}");

        #add attendee information

        $order->add_attendees(array_map(function($item) use ($ticket){ return $item + [ 'ticket_id' => $ticket->id];}, $attendees));

        if ($price){
            #create a payment record
            $payment = $order->create_payment('system',$order->amount);

            #finaly process payment
            Payment::complete_payment($payment,$order->user);
        }else{
            //complete the order and send tickets
            $order->status  = 'paid';
            $order->save();

            event(new OrderCompleted($order));
        }

        if($order)
            return response()->json($order, 200);

        return response()->json([
            'status' => 'fail',
            'code' => 'BT003',
            'message' => "Internal error, could not complete your request"
        ], 430);
    }

    /**
     * @param $channel
     * @param $code
     * @param $ref_no
     * @param $amount
     * @param Event $event
     * @param User $user
     * @return Payment|null
     */
    private function create_payment_from_request($channel, $code, $ref_no, $amount, Event $event, User $user, $notify = true)
    {
        $payment =  Payment::create_payment($channel, [
            'currency' => 'KES',
            'payment_key' => $code,
            'payment_ref' => $ref_no,
            'fee' => 0,
            'amount' => $amount,
            'total' => $amount ,
            'notes' => "Payment for {$event}, Reference No: #{$ref_no}",
        ], 'processing');
        $payment->payment_ref = $ref_no;

        if($notify)
            $payment->send_payment_received_notification($user->phone, "{$event}");

        return $payment;
    }


}
