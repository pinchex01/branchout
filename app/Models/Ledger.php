<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected  $table = 'ledgers';

    protected $fillable = [
        'account_id','notes','credit','debit','balance'
    ];

    /**
     * Get the referenced account
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
