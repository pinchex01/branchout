<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments =  Payment::filter($request)
            ->with(['order'])
            ->latest('payments.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.payments.index',[
            'payments' => $payments
        ])->with('page_title', "View Payments");
    }

    public function processPayment(Payment $payment, Request $request)
    {
        $payment->load(['order','order.user']);

        if (in_array($payment->status, ['paid','complete']) || !$payment->order){
            #payment is an orphan
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "An error occurred. Payment is an orphan"]
                ]);
        }

        Payment::complete_payment($payment,$payment->order->user);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'info', 'message' => "Payment is being processed"]
            ]);
    }
}
