<?php

namespace App\Jobs;

use App\Models\Attendee;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use Log;
use File;

class GenerateTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $ticket_no;
    protected $force;


    /**
     * Create a new job instance.
     *
     * @param Order $order
     * @param null $ticket_no
     * @param bool $force
     */
    public function __construct(Order $order, $ticket_no = null, $force = true)
    {
        $this->order = $order->load(['event']);
        $this->ticket_no  = $ticket_no;
        $this->force = $force;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->ticket_no){
            $this->order->attendees()->with(['ticket'])->each(function($attendee){
                $attendee->generate_ticket($this->force);
            });
        }else{
            $attendee = Attendee::get_by_ticket_no($this->ticket_no);
            $this->create_ticket($attendee);
        }
    }
}
