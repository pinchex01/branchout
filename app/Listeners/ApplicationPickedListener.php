<?php

namespace App\Listeners;

use App\Events\ApplicationPicked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationPickedListener
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
     * @param  ApplicationPicked  $event
     * @return void
     */
    public function handle(ApplicationPicked $event)
    {
        //
    }
}
