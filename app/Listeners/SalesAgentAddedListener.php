<?php

namespace App\Listeners;

use App\Events\SalesAgentAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalesAgentAddedListener
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
     * @param  SalesAgentAdded  $event
     * @return void
     */
    public function handle(SalesAgentAdded $event)
    {
        //
    }
}
