<?php

namespace App\Listeners;

use App\Events\EventApproved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventApprovedListener
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
     * @param  EventApproved  $event
     * @return void
     */
    public function handle(EventApproved $event)
    {
        //
    }
}
