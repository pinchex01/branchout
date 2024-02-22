<?php

namespace App\Models;

use App\Events\OrderCompleted;
use App\Jobs\SendSms;
use App\Lib\FundsTransfer;
use App\Mail\OrderCompletedNotification;
use App\Mail\OrderTickets;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use PDF;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'event_id', 'user_id', 'amount', 'date_paid', 'notes', 'order_date', 'status', 'receipt_path', 'sales_person_id',
        'first_name', 'last_name', 'email', 'phone', 'pk', 'refunded'
    ];

    protected $dates = ['order_date', 'date_paid', 'created_at', 'updated_at'];

    public static $ticket_storage_path = 'app/pdfs/tickets';

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function sales_person()
    {
        return $this->belongsTo(SalesPerson::class, 'sales_person_id');
    }

    public static function find_by_order_no($order_no)
    {
        return self::whereRefNo(strtoupper($order_no))->first();
    }

    public function scopeComplete($query)
    {
        return $query->whereIn('orders.status', ['paid','complete']);
    }

    public function scopePayment($query, $type  = '')
    {

        if (is_empty($type) || !in_array($type, ['paid','free']))
            return $query;

        if($type  == 'paid')
            return $query->whereRaw("orders.amount > 0");

        return $query->whereRaw('orders.amount < 1');
    }

    /**
     * Create order and return instance or the order with loaded relations
     *
     * @param $event_id
     * @param  $user_info
     * @param $amount
     * @param array $items
     * @param $notes
     * @param null $date_paid
     * @param SalesPerson|null $salesPerson
     * @return Order|null
     */
    public static function create_order($event_id, $user_info, $amount, array $items, $notes, $date_paid = null, SalesPerson $salesPerson = null)
    {
        $order = null;
        \DB::transaction(function () use ($event_id, $user_info, $amount, $items, $notes, $date_paid, &$order, &$salesPerson) {
            $order = new self([
                'event_id' => $event_id,
                'amount' => $amount,
                'notes' => $notes,
                'order_date' => Carbon::now(),
                'sales_person_id' => $salesPerson ? $salesPerson->id : null
            ]);

            $order->fill(map_props_to_params($user_info->toArray(), $order->getFillable()));
            if ($user_info->id) {
                $order->user_id = $user_info->id;
            }

            $order->status = 'pending';

            //check if order has been paid for
            if ($date_paid) {
                $order->date_paid = $date_paid;
                $order->status = 'paid';
            }
            $order->save();

            //save order items
            $order_items = array_map(function ($item) use ($order) {
              $price  = array_get($item, 'price',$item['ticket']['price']);
                return new OrderItem([
                    'order_id' => $order->id,
                    'ticket_id' => $item['ticket_id'],
                    'name' => $item['ticket']['name'],
                    'unit_price' => $price,
                    'quantity' => $item['quantity'],
                    'total' =>  $price * $item['quantity']
                ]);
            }, $items);
            $order->order_items()->saveMany($order_items);
            //load relations
            $order->load(['event', 'user', 'order_items', 'order_items.ticket']);
        });

        //todo: order has been create, generate event ticket pdfs and send on mail if possible
        return $order;
    }

    /**
     * Update ticket quantity sold for the order items
     * @param bool $cancelled
     * @return
     */
    public function update_ticket_sale($cancelled = false)
    {
        return $this->order_items->each(function ($order_item) use( $cancelled) {
            $order_item->ticket_sold($cancelled);
        });
    }


    /**
     * Boot all of the bootable traits on the model.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->ref_no = generate_random_string();
            $order->pk = \Uuid::generate()->string;
        });

        /*
        static::created(function($order){
            #get pesaflow payment_ref
            if(!$order->is_complete())
                $order->get_pesaflow_payment_ref();
        });
        */
    }

    public function get_sales_person_commission($amount = null)
    {
        if (!$this->sales_person_id)
            return 0;

        $amount = $amount ?: $this->amount;
        return ($this->event->commission * $amount) / 100;
    }

    /**
     * Send order notification to user
     * @param User|null $user
     */
    public function send_order_processed_notification(User $user = null)
    {
        $order = $this->load(['user', 'event', 'event.organiser', 'order_items']);
        $user = $user ?: $order->get_user_info();

        #send sms for complete order
        $link = route('auth.otp',['ref' => $user->pk, 'go_to' => route('app.orders.view', $order->id)]);
        $message = "Hi, {$user->first_name}. Your ticket(s) for {$order->event} have been generated. Your Order Number is: {$order->ref_no}. Download your ticket from: {$link}";
        dispatch(new SendSms($user->phone, $message));

        #only send email when user has email address
        if ($user->email && settings('enable_order_email', false)) {
            \Mail::to($user)
                ->send(new OrderCompletedNotification($order));
        }
    }

    public function get_user_info()
    {
        return $this->user ?: new User([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone
        ]);
    }

    /**
     * Send order tickets
     * @param User|null $user
     */
    public function send_order_tickets(User $user = null)
    {
        $order = $this->load(['user', 'event', 'event.organiser', 'order_items']);
        $user = $user ?: $order->user;

        \Mail::to($user)
            ->send(new OrderTickets($order));
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validate_tickets_purchase(Request $request)
    {
        $user = $request->user();
        $raw_tickets = $request->input('tickets');

        //only pick tickets with quantity over zero selected, ignore the reset
        $tickets = [];
        foreach ($raw_tickets as $k => $v) {
            if ($v > 0) {
                $tickets[$k] = $v;
            }
        }

        $purchasable_tickets = [];
        $errors = [];
        $_tickets = Ticket::whereIn('id', array_keys($tickets))->get();
        $total_purchase = 0;
        $_tickets->each(function ($ticket) use ($tickets, &$errors, &$purchasable_tickets, &$total_purchase) {
            $quantity = $tickets[$ticket->id];
            $quantity_remaining = $ticket->quantity_remaining;
            //ensure ticket is still on sale
            if ($ticket->status != 'on-sale')
                $errors["tickets.{$ticket->id}"][] = "{$ticket} have been sold out";

            //check that quantity is at lease min per order
            elseif ((!$quantity || $ticket->min_per_person > $quantity) && !$ticket->is_group_ticket) {
                $errors["tickets.{$ticket->id}"][] = "You can only buy at least {$ticket->min_per_person} of {$ticket} tickets";
            } //ensure the quantity selected is available

            elseif ($quantity_remaining < $quantity)
                $errors["tickets.{$ticket->id}"][] = "Only {$quantity_remaining} ticket(s) are remaining";

            //ensure user does not over order
            elseif ($ticket->max_per_person < $quantity) {
                $errors["tickets.{$ticket->id}"][] = "You can only buy up to {$ticket->max_per_person} of {$ticket} tickets";
            } else {
                $purchasable_tickets[$ticket->id] = $quantity;

            }
            $total_purchase += $ticket->unit_price * $quantity;
        });

        //validate that user has sufficient points to complete purchase if payment method is points
        if ($request->input('channel') == 'points' && $total_purchase >= $user->points)
            $errors['alerts'][] = ['type' => 'info', 'message' => "You do not have sufficient points to complete this purchase"];

        return [$purchasable_tickets, $errors];
    }

    public function add_attendees(array $attendees)
    {
        $order = $this;
        $event = $this->event;
        $organiser = $event->organiser;
        $tickets = array_map(function ($attendee) use ($order, $event) {
            $_attendee = new Attendee();
            $_attendee->fill(map_props_to_params($attendee, $_attendee->getFillable()));
            $_attendee->event_id = $event->id;
            $_attendee->order_id = $order->id;
            $_attendee->pk = \Uuid::generate()->string;
            return $_attendee;
        }, $attendees);

        $order->attendees()->saveMany($tickets);

        return $this->fresh(['attendees']);
    }

    public static function complete_order(self $order, $amount_paid)
    {

        $order->status  = 'paid';
        $order->save();

        event(new OrderCompleted($order));
    }

    public function create_payment($channel, $amount = null)
    {
        $user = $this->user;
        $event = $this->event;

        $payment = Payment::create_payment($channel, [
            'currency' => 'KES',
            'fee' => 0,
            'payment_key' => time(),
            'amount' => $amount ?: $this->amount,
            'total' => $amount ?: $this->amount,
            'order_id' => $this->id,
            'notes' => "Payment for {$event}, invoice #{$this->ref_no}",
        ], $user);

        return $payment;
    }

    public function send_payment_notification()
    {
        $user = $this->user;
        $paybill_no = settings('paybill_no');
        $message = "HI, {$user->first_name}. Please pay KES {$this->amount} to MPESA Paybill: {$paybill_no}  Account No: {$this->ref_no}. To complete your ticket purchase";
        dispatch(new SendSms($user->phone, $message));

    }

    public function pay_with($channel)
    {
        $user = $this->user->load(['account']);

    }

    public function get_status_label()
    {
        switch ($this->status) {
            case 'paid':
                return '<span class="label label-success"><i class="fa fa-check"></i> paid </span>';
            case 'processing':
                return '<span class="label label-info"><i class="fa fa-info"></i> processing </span>';
            case 'unpaid':
                return '<span class="label label-danger"><i class="fa fa-ban"></i> not paid </span>';
            case 'cancelled':
                return '<span class="label label-default"><i class="fa fa-trash"></i> cancelled </span>';
            case 'pending':
                return '<span class="label label-danger"><i class="fa fa-ban"></i> not paid </span>';
            default:
                break;
        }
    }

    /**
     * @param Request $request
     * @return Builder
     */
    public static function filter(Request $request)
    {
        $builder = self::query();

        $filters = $request->get('filters');
        if ($filters){
            foreach ($filters as $key => $value){

                //if value is not empty
                if (trim($value) != ""){
                    switch ($key){
                        case 'order_no':
                            $builder->where('ref_no', strtoupper($value));
                            break;
                        case 'event_id':
                            $builder->where('orders.event_id', $value);
                            break;
                        case 'status':
                            $builder->where('orders.status',$value);
                            break;
                        case 'organiser_id':
                            $builder->whereHas('event', function($q) use ($value){
                                return $q->where('events.organiser_id', $value);
                            });
                            break;
                        case 'phone':
                            $builder->where('orders.phone', $value);
                            break;
                        case 'payment':
                            $builder->payment($value);
                            break;
                        default:
                            break;
                    }
                }

            }

        }

        return $builder;
    }

    /**
     * @param bool $force
     * @return mixed
     */
    public function generate_tickets($force = false)
    {
        return $this->attendees()->with(['ticket'])->each(function($attendee) use ($force) {
            $attendee->generate_ticket($force);
        });
    }

    /**
     * @return bool
     */
    public function is_complete()
    {
        return in_array($this->status, ['paid', 'complete']);
    }

    /**
     * @param Order $order
     * @return Order
     */
    public static function cancel(self $order, $refund = false)
    {
        \DB::beginTransaction();
            $order->load(['sales_person','user','payments']);
            $user = $order->user;

            #funds transfer reversal
            $ft  = new FundsTransfer();
            list($amount, $commission) = $ft->cancel_order($order, $refund);

            if($order->sales_person)
                $order->sales_person->reverse_sales($order,$commission);

            #reverse payments
            $order->payments()->each(function ($payment) use ($user, $refund) {
                Payment::reverse_payment($payment, $user, $refund);
            });

            #update tickets sale info
            $order->update_ticket_sale(true);

            #finally mark the order as cancelled
            $order->update(['status' => 'cancelled', 'refunded' => $refund]);
        \DB::commit();

        return $order;
    }

    public function send_order_canceled_message()
    {
        #don't notify user if no refund was issue
        if (!$this->refunded) return null;

        $order = $this->load(['user', 'event']);
        $user = $this->user;

        #send sms
        $message = "Hi, {$user->first_name}. Your order no #{$order->ref_no} for {$order->event} ticket(s) has been cancelled. KES {$order->amount} has been refunded to your PartyPeople wallet. Login to http:/partypeople.co.ke to make another purchase using your wallet. Thank you";
        dispatch(new SendSms($user->phone, $message));
    }

    /**
     * [has_expired_tickets description]
     * @return boolean [description]
     */
    public function has_expired_tickets()
    {
      return !!$this->order_items()->whereHas('ticket', function($query){
          $now  = date('Y-m-d H:i');
        $query->whereRaw("'{$now}' > tickets.end_sale_date");
      })->count();
    }

    /**
    * Call pesaflow and get payment ref
    */
    public function get_pesaflow_payment_ref()
    {
        #get pesaflow payment ref
        $ref  = create_pesaflow_bill($this->ref_no, intval(round($this->amount)), $this->notes, $this->user);
        $this->payment_ref =  $ref;
        $this->save();

        return $ref;
    }

    public function get_payment_signature($currency = 'KES')
    {
        $config  =  config('pesaflow');
        $user  =  $this->user;

        return sign_pesaflow_payload([
          $config['apiClientId'], intval(round($this->amount)), $config['apiServiceId'], $user->id_number,
          $currency, $this->ref_no, $this->notes, $user->full_name, $config['apiSecret']
        ], $config['apiKey']);
    }

    public function get_pesaflow_checkout_data($currency = 'KES')
    {
        $config  = config('pesaflow');
        $user =  $this->user;

        if(!$this->payment_ref)
            $this->get_pesaflow_payment_ref();

        return [
            'url' => $config['url'],
            'apiClientID' => $config['apiClientId'],
            'secureHash' => $this->get_payment_signature(),
            'currency' => $currency,
            'billDesc' => $this->notes,
            'billRefNumber' => $this->ref_no,
            'serviceID' => $config['apiServiceId'],
            'clientMSISDN' => $user->phone,
            'clientName' => $user->full_name,
            'clientIDNumber' => $user->id_number,
            'clientEmail' => $user->email,
            'amountExpected' => $this->amount,
            'callBackURLOnSuccess' => route('app.orders.view', [ $this->id]),
            'pictureURL' => $user->getAvatar(),
            'notificationURL' => route('api.purchase_ticket'),
            'callBackURLOnFail' => route('app.orders.view', [ $this->id , 'status' =>'fail']),
        ];
    }
}
