<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedListener
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
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $password  = null;
        $user  = $event->user;

        if($event->generate_password){
            $password = str_random(7);
            $user->password = $password;
            $user->change_password =  1;
            $user->save();
        }

        $user->send_welcome_notification($password);
    }
}
