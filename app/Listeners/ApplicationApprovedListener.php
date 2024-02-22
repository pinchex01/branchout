<?php

namespace App\Listeners;

use App\Events\ApplicationApproved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationApprovedListener
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
     * @param  ApplicationApproved  $event
     * @return void
     */
    public function handle(ApplicationApproved $event)
    {
        //
    }
}
