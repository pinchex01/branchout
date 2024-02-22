<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderTickets extends Mailable
{
    use Queueable, SerializesModels;

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
        $this->order  = $order;
        $this->event = $order->event;
        $this->user  = $order->user;
        $this->organiser = $order->event->organiser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.order_tickets')
            ->subject('Your tickets for the event ' . $this->event->name)
            ->attach(storage_path($this->order->get_ticket_path()));
    }
}
