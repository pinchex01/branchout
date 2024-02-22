<?php

namespace App\Listeners;

use App\Events\ApplicationCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationCreatedListener
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
     * @param  ApplicationCreated  $event
     * @return void
     */
    public function handle(ApplicationCreated $event)
    {
        //
    }
}
