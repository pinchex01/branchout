<?php

namespace App\Http\Controllers\Api;

use App\Lib\FundsTransfer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function completePayment(Request $request)
    {
        $this->validate($request, [
            'payment_key' => [
                "required",
                Rule::exists('payments', 'payment_key')
                    ->whereNot('status', 'complete')
            ],
        ]);

        $payment = Payment::where('payment_key', $request->input('payment_key'))
            ->whereNotIn('status', ['complete'])
            ->firstOrFail();

        list ($x, $order) = Payment::complete_payment($payment);

        if ($order) {
                return response()->json([
                    "status" => "ok",
                    "message" => "Payment completed successfully"
                ], 200);

        } else {
            return response()->json([
                "status" => "fail",
                "message" => "Error occurred! Payment could not be completed."
            ], 500);

        }
    }

    public function pesaflowIpnEndpoint(Request $request)
    {
        \Log::info("Pesaflow IPN Received: ", $request->all());
        list($status, $msg) = $this->validatePesaflow($request);

        \Log::info("Pesaflow IPN Validation status {$status}: ", ["message" => $msg]);

        if (!$status)
            return response()->json([
                'query_status' => $msg
            ], 500);

        $payment  = $msg;

        list ($x, $order) = Payment::complete_payment($payment);

        if ($order) {
            return response()->json([
                "status" => "ok",
                "message" => "Payment completed successfully"
            ], 200);

        } else {
            return response()->json([
                "status" => "fail",
                "message" => "Error occurred! Payment could not be completed."
            ], 500);

        }
    }

    /**
     * Validate pesaflow ipn request
     *
     * @param Request $request
     * @return array
     */
    public function validatePesaflow(Request $request)
    {
        //if($request->input('api_key') != config('pesaflow.apiKey') || !\App::environment('dev','development'))
          //  return [false, "unauthorized"];

        $payment = Payment::get_by('payment_key', $request->input('invoiceNumber'));
        if(!$payment || $payment->status != 'unpaid')
            return [false, "invalid invoice"];

        //if($payment->payment_ref != $request->input('transaction_id'))
            //return [false, "invalid transaction id"];

        if(floatval($payment->total) < floatval($request->input('amount')))
            return [false, "invalid transaction amount"];

        return [true, $payment];
    }

    public function completeCheckout(Request $request)
    {
        $this->validate($request, [
            'order_ref' => "required|exists:orders,pk",
            'channel' => "required|in:mpesa,points,wallet",
        ]);
        $order = Order::wherePk($request->input('order_ref'))->first();
        if (in_array($order->status, ['active','complete','paid'])){
            return response()->json([
                'status' => 'ok',
                "message" => "Payment received successfully!"
            ]);
        }

        $channel  = $request->input('channel');
        $user  = $order->user->load(['account']);

        //validate that payment can be completed with points
        if($channel == 'points' && $user->points < $order->amount) {
            $bal = $order->amount  -  $user->points;
            return response()->json([
                'status' => 'fail',
                "message" => "You have insufficient points to complete this purchase.  {$bal} more point(s) needed."
            ], 430);
        }

        //validate that payment can be completed with wallet balance
        if($channel == 'wallet' && $user->account->balance < $order->amount) {
            $bal = $order->amount  -  $user->account->balance;
            return response()->json([
                'status' => 'fail',
                "message" => "Insufficient balance. You need {$bal} more to complete the purchase"
            ], 430);
        }

        //check if any payments has been received
        if($channel == 'mpesa' && $order->status  == 'pending' ) {

            return response()->json([
                'status' => 'fail',
                "message" => "No payment has been received for this purchase. Please follow the instructions below to pay"
            ], 430);
        }

        #everything looks good, complete the purchase
        $payment  = $order->create_payment($channel, $order->amount);
        Payment::complete_payment($payment, $order->user);

        return response([
            'status' => "ok",
            "message" => "Payment received successfully, your order is being processed!"
        ]);

    }
}
