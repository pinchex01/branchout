<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskCompletedListener
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
     * @param  TaskCompleted  $event
     * @return void
     */
    public function handle(TaskCompleted $event)
    {
        //
    }
}
