<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Jobs\GenerateTicket;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCompletedListener implements ShouldQueue
{
    use DispatchesJobs;

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
     * @param  OrderCompleted  $event
     * @return void
     */
    public function handle(OrderCompleted $event)
    {
        $order = $event->order;
        $this->dispatch(new GenerateTicket($order));
        $order->send_order_processed_notification();
    }
}
