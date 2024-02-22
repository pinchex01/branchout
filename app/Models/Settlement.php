<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $table = 'settlements';

    protected $fillable = [
        'account_id', 'account_type', 'conversation_id', 'amount', 'date_settled','bank','account_no','account_name',
        'status','notes'
    ];

    public function account()
    {
        switch ($this->account_type) {
            case 'Bank';
                return $this->belongsTo(BankAccount::class, 'account_id');
            case  'User';
                return $this->belongsTo(User::class, 'account_id');
        }
    }

    public static function create_new_settlement($account_id, $account_type, $amount, $notes, $account_no, $account_name, $bank)
    {
        $settlement = new self;
        $settlement->account_id  = $account_id;
        $settlement->account_type  = $account_type;
        $settlement->amount =  $amount;
        $settlement->notes  = $notes;
        $settlement->account_no =  $account_no;
        $settlement->account_name  =  $account_name;
        $settlement->bank  = $bank;
        $settlement->save();

        return $settlement;
    }

    /**
     * @return string
     */
    public function get_paybill()
    {
        if($this->account_type  == 'User')
            return $this->account->phone;

        return $this->account->bank ? $this->account->paybill : $this->account->account_no;
    }

    public function get_status()
    {
        switch ($this->status)
        {
            case 'pending':
                return '<span class="label label-warning">Processing</span>';
                break;
            case 'failed':
                return '<span class="label label-danger">Failed</span>';
                break;
            case 'processed':
                return '<span class="label label-success">Complete</span>';
                break;
            default:
                break;
        }
    }
}
