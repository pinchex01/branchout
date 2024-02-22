<?php

namespace App\Listeners;

use App\Events\ApplicationRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationRejectedListener
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
     * @param  ApplicationRejected  $event
     * @return void
     */
    public function handle(ApplicationRejected $event)
    {
        //
    }
}
