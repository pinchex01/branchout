<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $table  = 'payment_logs';

    protected $fillable  = [
      'txn_no','username','ref','amount','status', 'notes'
    ];

    /**
     * Mark a payment log as complete
     * 
     * @param  [type] $username [description]
     * @param  [type] $notes    [description]
     * @return self|null           [description]
     */
    public static function complete_payment($username, $notes)
    {
      $payment = self::query()->where('username', $username)->where('status','confirmed')->first();
      if(!$payment)
        return null;

      $payment->update([ 'status' => 'complete', 'notes' => $notes]);

      return $payment;
    }
}
