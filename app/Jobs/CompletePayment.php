<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CompletePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payment;
    public $phone;
    /**
     * Create a new job instance.
     *
     * @param Payment $payment
     * @param null $phone
     */
    public function __construct(Payment $payment, $phone = null)
    {
        $this->payment  = $payment;
        $this->phone  = $phone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payment = $this->payment;
        $order = $payment->order;
        $user = $order->user;
        $phone  = $this->phone ? : $user->phone;

        //process payment
        Payment::complete_payment($payment, $user);
    }

}
