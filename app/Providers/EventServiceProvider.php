<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LoginSuccessListener',
        ],
        'App\Events\OrderCompleted' => [
            'App\Listeners\OrderCompletedListener',
        ],
        'App\Events\OrderCancelled' => [
            'App\Listeners\OrderCancelledListener',
        ],
        'App\Events\ApplicationCreated' => [
            'App\Listeners\ApplicationCreatedListener',
        ],
        'App\Events\ApplicationReviewed' => [
            'App\Listeners\ApplicationReviewedListener',
        ],
        'App\Events\ApplicationPicked' => [
            'App\Listeners\ApplicationPickedListener',
        ],
        'App\Events\ApplicationApproved' => [
            'App\Listeners\ApplicationApprovedListener',
        ],
        'App\Events\OrganiserActivated' => [
            'App\Listeners\OrganiserActivatedListener',
        ],
        'App\Events\BankActivated' => [
            'App\Listeners\BankActivatedListener',
        ],
        'App\Events\TaskCreated' => [
            'App\Listeners\TaskCreatedListener',
        ],
        'App\Events\TaskCompleted' => [
            'App\Listeners\TaskCompletedListener',
        ],
        'App\Events\ApplicationRejected' => [
            'App\Listeners\ApplicationRejectedListener',
        ],
        'App\Events\EventApproved' => [
            'App\Listeners\EventApprovedListener',
        ],
        'App\Events\SalesAgentAdded' => [
            'App\Listeners\SalesAgentAddedListener',
        ],
        'App\Events\CheckIn' => [
            'App\Listeners\CheckInListener',
        ],
        'App\Events\UserCreated' => [
            'App\Listeners\UserCreatedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
