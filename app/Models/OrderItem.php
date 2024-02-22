<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable  = [
        'order_id', 'ticket_id', 'quantity', 'unit_price', 'total','name'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function ticket_sold($reverse = false)
    {
        return $this->ticket->increase_quantity_sold($reverse ? -$this->quantity : $this->quantity);
    }
}
