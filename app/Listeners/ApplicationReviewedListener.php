<?php

namespace App\Listeners;

use App\Events\ApplicationReviewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationReviewedListener
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
     * @param  ApplicationReviewed  $event
     * @return void
     */
    public function handle(ApplicationReviewed $event)
    {
        //
    }
}
