<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCancelledListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCancelled  $event
     * @return void
     */
    public function handle(OrderCancelled $event)
    {
        //todo: add other related stuff here
        $order = $event->order;

        $order->send_order_canceled_message();
    }
}
