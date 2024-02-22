<?php

namespace App\Listeners;

use App\Events\OrganiserActivated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganiserActivatedListener
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
     * @param  OrganiserActivated  $event
     * @return void
     */
    public function handle(OrganiserActivated $event)
    {
        //
    }
}
