<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var Order
     */
    public $order;
    public $user;
    public $event;
    public $organiser;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->event = $order->event;
        $this->user  = $order->get_user_info();
        $this->organiser = $order->event->organiser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view  = $this->view('emails.order_complete_notification')
            ->subject('Your tickets for the event ' . $this->event->name);

        //add ticket attach
        $this->order->attendees->each(function ($attendee) use (&$view){
            $view->attach(storage_path($attendee->get_ticket_path()));
        });

        return $view;
    }
}
