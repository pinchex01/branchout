<?php

namespace App\Models;

use App\Events\SalesAgentAdded;
use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    protected $table = 'sales_people';

    protected $fillable = [
        'event_id', 'organiser_id', 'code', 'tickets_sold', 'fees', 'total', 'status'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organiser()
    {
        return $this->belongsTo(Organiser::class, 'organiser_id');
    }

    public static function get_by_code($code)
    {
        return self::where('code',$code)->first();
    }

    /**
     * Create sales person from event and user
     *
     * @param Event $event
     * @param Organiser $organiser
     * @param string $status
     * @return SalesPerson
     */
    public static function add_sales_person(Event $event, Organiser $organiser, $status = 'active')
    {
        $sales_person = new self();
        $sales_person->event_id  =  $event->id;
        $sales_person->organiser_id = $organiser->id;

        $sales_person->code = $organiser->code;
        $sales_person->status = $status;
        $sales_person->save();

        event(new SalesAgentAdded($sales_person));

        return $sales_person;
    }

    public function add_sales(Order $order, $commission = 0)
    {
        //update sales person sales records
        $tickets = $order->order_items()->sum('quantity');

        //calculate commission only if necessary
        $commission = $commission ? : $order->get_sales_person_commission();

        $this->tickets_sold += $tickets;
        $this->fees += $commission;
        $this->total += $order->amount;
        $this->save();

        return $this;
    }

    public function reverse_sales(Order $order, $commission)
    {
        //update sales person sales records
        $tickets = $order->order_items()->sum('quantity');

        $this->tickets_sold += -$tickets;
        $this->fees += -$commission;
        $this->total += -$order->amount;
        $this->save();

        return $this;
    }
}
