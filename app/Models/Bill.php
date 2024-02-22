<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = ['bill_no', 'account','amount','paid_by'];


    /**
    * Create bill
    */
    public static function create_bill($bill_no, $account, $amount, $paid_by, $status  = 'paid')
    {
        $bill  = new self;
        $bill->fill([
            'bill_no' => $bill_no,
            'account' => $account,
            'paid_by' => $paid_by,
            'amount' => $amount
        ]);

        $bill->save();
        return $bill;
    }
}
