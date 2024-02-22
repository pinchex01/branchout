<?php

namespace App\Listeners;

use App\Events\BankActivated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BankActivatedListener
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
     * @param  BankActivated  $event
     * @return void
     */
    public function handle(BankActivated $event)
    {
        //
    }
}
