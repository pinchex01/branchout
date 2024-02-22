<?php

namespace App\Listeners;

use App\Events\CheckIn;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckInListener
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
     * @param  CheckIn  $event
     * @return void
     */
    public function handle(CheckIn $event)
    {
        //
    }
}
