<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class GenerateTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate event tickets for complete orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Generating tickets");

        Order::query()->complete()->chunk(200, function ($orders){
            foreach ($orders as $order){
                $order->attendees()->with(['ticket'])->each(function($attendee){
                    $attendee->generate_ticket($this->force);
                });
            }
        });

        $this->info("Tickets generated successfully");
    }


}
