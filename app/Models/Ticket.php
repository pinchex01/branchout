<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    public static $statues = [
        'on-sale' => 'On Sale',
        'on-hold' => 'On Hold',
        'sold-out' => 'Sold Out'
    ];

    protected $fillable = [
        'name', 'event_id', 'description', 'min_per_person', 'max_per_person', 'price', 'quantity_available',
        'quantity_sold', 'on_sale_date', 'end_sale_date', 'sales_volume', 'status','public_id'
    ];

    protected $casts  = [
        'on_sale_date' => 'datetime', 'end_sale_date' => 'datetime'
    ];

    public function __toString()
    {
        return $this->name;
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class, 'ticket_id');
    }

    /**
     * Get the number of tickets remaining.
     *
     * @return \Illuminate\Support\Collection|int|mixed|static
     */
    public function getQuantityRemainingAttribute()
    {
        if (is_null($this->quantity_available)) {
            return 999999; //Better way to do this?
        }

        return $this->quantity_available - $this->quantity_sold;
    }

    /**
     * Get the number of tickets reserved.
     *
     * @return mixed
     */
    public function getQuantityReservedAttribute()
    {
        $reserved_total = \DB::table('reserved_tickets')
            ->where('ticket_id', $this->id)
            ->where('expires_at', '>', \Carbon::now())
            ->sum('quantity');

        return $reserved_total;
    }

    public function scopeOnSale($query)
    {
        $now  = date('Y-m-d H:i');
        return $query->whereRaw("'{$now}' >= tickets.on_sale_date AND '{$now}' <= tickets.end_sale_date");
    }

    public static function add_ticket($event_id, $props)
    {
        $ticket = new self;
        $data  = map_props_to_params(array_except($props, ['on_sale_date','end_sale_date']), $ticket->fillable);
        $ticket->event_id  = $event_id;
        $ticket->fill($data);
        $ticket->on_sale_date = Carbon::parse($props['on_sale_date']);
        $ticket->end_sale_date  = Carbon::parse(array_get($props, 'end_sale_date',$props['on_sale_date']));
        $ticket->save();

        return $ticket;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($ticket){
            $ticket->public_id  = md5(time());
            $ticket->price = $ticket->price? :  0;
        });
    }

    /**
     * Find ticket public id
     *
     * @param $public_id
     * @return mixed
     */
    public static function find_by_public_id($public_id)
    {
        return static::wherePublicId($public_id)->first();
    }

    /**
     * Increase quantity of sold tickets, if no more tickets change to sold out
     *
     * @param $quantity
     * @return Ticket
     */
    public function increase_quantity_sold($quantity)
    {
        $ticket = $this;
        \DB::transaction(function () use ($quantity, &$ticket){
            $ticket->quantity_sold += $quantity;

            //check if ticket is now sold out, if quantity_available is not set, ignore coz they are unlimited
            if ($ticket->quantity_avaiable && $ticket->quantity_available <= ($quantity - $ticket->quantity_sold))
                $ticket->status = 'sold-out';

            $ticket->save();
        });

        return $ticket;
    }

    public function reserve($session_id, $quantity, $expires_at, User $user = null, $group = 1)
    {
        return ReservedTicket::reserve($session_id, $this->event, $this, $quantity, $expires_at, $user, $group);
    }
     /**
    *
    */
    public function setOnSaleDateAttribute($value)
    {
        $this->attributes['on_sale_date'] = Carbon::parse($value)->format('Y-m-d H:m:s');
    }

    /**
     * @param $value
     */
    public function setEndSaleDateAttribute($value)
    {
        $this->attributes['end_sale_date'] = Carbon::parse($value)->format('Y-m-d H:m:s');
    }

    /**
     * Check if ticket is on sale from dates
     */
    public function getOnSaleAttribute()
    {
        $current = Carbon::now()->format('Y-m-d');
        $start_sale  = $this->on_sale_date->format("Y-m-d");
        $end_sale  = $this->end_sale_date->format("Y-m-d");

        return ($start_sale <= $current && $end_sale >= $current);
    }

    public static function get_cost($ticket_id, $quanity)
    {
        $ticket = self::findOrFail($ticket_id);

        return $ticket->price * $quanity;
    }

    /**
    * Get total cost ticket quantity pairs
    */
    public static function get_total_cost(array $tickets)
    {
        $total  = 0;
        Ticket::whereIn('id', array_keys($tickets))
            ->each(function ($ticket) use ($tickets, &$total) {
                    $total += ($ticket->price * $tickets[$ticket->id]);
            });

        return $total;
    }
}
