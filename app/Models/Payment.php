<?php

namespace App\Models;

use App\Events\OrderCompleted;
use App\Jobs\CompletePayment;
use App\Jobs\SendSms;
use App\Lib\FundsTransfer;
use Carbon\Carbon;
use Httpful\Mime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'payment_key', 'order_id', 'payments_ref', 'currency', 'fee', 'amount', 'total',
        'status', 'channel', 'date_paid', 'notes', 'payload', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = empty($value) ? json_encode([]) : json_encode($value);
    }

    public function getPayloadAttribute()
    {
        return json_decode($this->attributes['payload'], true);
    }

    public function getRawSchema()
    {
        return $this->payload;
    }

    /**
     * Create payment
     *
     * @param $channel
     *
     * @param $props
     * @param string $status
     * @return Payment|null
     */
    public static function create_payment($channel, $props, $status = 'pending')
    {
        $payment = new self();
        $data = map_props_to_params($props, $payment->fillable);
        $payment->fill($data);
        $payment->channel = $channel;
        $payment->status  = $payment->status ? : 'pending';
        $payment->save();

        return $payment;
    }

    public static function complete_payment(self $payment, User $user)
    {
        //save order as processing first
        $order = $payment->order;
        $order->update(['status' => 'processing']);

        \DB::transaction(function () use (&$payment, &$order, &$user) {
            $event  = $order->event;

            //if payment channel was points, deduct from user points
            if ($payment->channel == 'points')
                $user->add_points(-$payment->total);

            $payment->date_paid = Carbon::now();
            $payment->status = 'complete';
            $payment->save();

            //award points to the user with value of purchase
            $points_earned = static::get_points_earned($payment);
            if ($points_earned) {
                $user->add_points($points_earned);
            }

            //process payment if tickets were not free
            $ft = new FundsTransfer();
            list($amount, $commission) = $ft->process_payment($order, $payment, $order->sales_person);

            //post sales statistics for sales person
            if ($sales_person = $order->sales_person)
                $sales_person->add_sales($order, $commission);

            //complete the order and send tickets
            $order->status  = 'paid';
            $order->save();

            $event->post_order_sales($order);

            //update order sale stats
            $order->update_ticket_sale();
        });

        #if order was successfully paid for generate ticket and send notifications
        if ($order)
            event(new OrderCompleted($order));

        return [$payment, $order];
    }

    /**
     * @param Payment $payment
     * @param User $user
     * @return Payment
     */
    public static function reverse_payment(self $payment, User $user, $refund = false)
    {

        $order = $payment->order;
        \DB::transaction(function () use (&$payment, &$order, &$user, $refund) {
            $event  = $order->event;

            //if payment channel was points, refund points
            if ($payment->channel == 'points' && $refund){
                $user->add_points($payment->total);
            }
            elseif($refund && !$payment->channel != 'points'){
                $user->credit($payment->total, $order->ref_no,"Order #{$order->ref_no} for {$order->event} cancelled");
            }

            #deduct points that were previous awarded for purchase
            $points_earned = static::get_points_earned($payment);
            if ($points_earned) {
                $user->add_points(-$points_earned);
            }

            #reverse order sales cache from event
            $event->post_order_sales($order, true);

            $payment->date_paid = Carbon::now();
            $payment->status = 'reversed';
            $payment->save();
        });

        return $payment;
    }

    /**
     * Get points earned from payment
     *
     * @param Payment $payment
     * @return int
     */
    public static function get_points_earned(Payment $payment)
    {
        $amount = $payment->total;

        $points = 0;
        //only earn points for purchase greater than 100
        if ($amount >= 100) { //should also configure lowest amount in settings
            $points = floor($amount * (settings('points_per_shilling', 1) / 100));
        }

        return intval($points);
    }

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();
    }

    public static function get_by($column, $value)
    {
        return self::where($column, $value)->first();
    }

    public function send_payment_received_notification($phone, $notes)
    {
        //notify user of payment
        $message = "Hi. Your payment of KES {$this->amount} has been received. Your order for {$notes} being processed. Once processed information on how to download your ticket will be sent to you.";
        dispatch(new SendSms($phone, $message));
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
                        case 'payment_ref':
                            $builder->where('payment_ref', $value);
                            break;
                        case 'payment_key':
                            $builder->where('payment_key', $value);
                            break;
                        case 'status':
                            $builder->where('payments.status',$value);
                            break;
                        case 'channel':
                            $builder->where('channel',$value);
                            break;
                        case 'phone':
                            $builder->whereHas('order', function($query) use ($value){
                                return $query->where('orders.phone',$value);
                            });
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
     * @return string
     */
    public function get_status_label()
    {
        switch ($this->status) {
            case 'paid':
                return '<span class="label label-warning"><i class="fa fa-check"></i> paid </span>';
            case 'complete':
                return '<span class="label label-success"><i class="fa fa-check"></i> complete </span>';
            case 'processing':
                return '<span class="label label-info"><i class="fa fa-info"></i> processing </span>';
            case 'unpaid' || 'pending':
                return '<span class="label label-danger"><i class="fa fa-times"></i> pending </span>';
            case 'reversed':
                return '<span class="label label-default"><i class="fa fa-trash"></i> reversed </span>';
            default:
                break;
        }
    }
}
