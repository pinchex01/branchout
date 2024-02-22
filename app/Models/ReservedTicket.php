<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReservedTicket extends Model
{
    protected $table = 'reserved_tickets';

    protected $fillable = [
        'event_id', 'ticket_id', 'user_id', 'quantity', 'expires_at', 'session_id', 'groups_of'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * @param $session_id
     * @return mixed
     */
    public static function get_reservations($session_id)
    {
        $reservations =  self::query()
            ->with(['ticket','event'])
            ->where('session_id', $session_id)->get();

        if(!$reservations)
            return false;

        $total  =0;
        $reservations->each(function($reservation) use (&$total) {
            $total += $reservation->ticket->price * $reservation->quantity;
        });

        return [
            $session_id,
             $total,
            $reservations
        ];
    }

    /**
     * @param $session_id
     * @return mixed
     */
    public static function cancel_reservation($session_id)
    {
        return self::where('session_id', $session_id)->delete();
    }

    public static function reserve($session_id, Event $event, Ticket $ticket, $quantity, $expires_at, User $user = null, $group = 1)
    {
        $reserved_ticket = null;
        \DB::transaction(function () use ($session_id, $event, &$ticket, $user, $quantity, $expires_at, &$reserved_ticket, $group){
            $reserved_ticket = new self([
                'event_id' => $event->id,
                'ticket_id' => $ticket->id,
                'quantity' => $quantity,
                'expires_at' => $expires_at,
                'session_id' => $session_id,
                'groups_of' => $group
            ]);
            if($user){
                $reserved_ticket->user_id  = $user->id;
            }

            $reserved_ticket->save();
        });

        return $reserved_ticket;
    }

    public static function create_order_from_reservation($cart_id, Event $event, User $user, array $attendees = null, SalesPerson $salesPerson = null)
    {
        $order = null;
        \DB::beginTransaction();
            list($cart_id, $total, $reserved_tickets)  =  self::get_reservations($cart_id);
            if(!$reserved_tickets->count()){
                \DB::rollback();
                return null;
            }
            $order  = Order::create_order($event->id, $user,$total,$reserved_tickets->toArray(),"Order for {$event}", null, $salesPerson);
            $order->status  = 'pending';
            $order->save();
            $order->add_attendees($attendees);

            //clear reservations details
            self::cancel_reservation($cart_id);
        \DB::commit();

        return $order;
    }


}
