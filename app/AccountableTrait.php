<?php

namespace App;


use App\Models\Account;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait AccountableTrait
{
    /**
     * ID column of the model that has account trait
     *
     * @var string
     */
    protected $id_column  = 'id';


    public function getAccountId()
    {
        return $this->{$this->id_column};
    }

    /**
     *
     */
    public function getAccountType()
    {
        return $this->account_type;
    }

    /**
     * Get the account
     *
     * @return HasOne
     */
    public function account()
    {
        return $this->hasOne(Account::class,'owner_id',$this->id_column)
            ->where('owner_type',$this->getAccountType());
    }

    public function getAccount()
    {
        return $this->account ? : new Account();
    }

    /**
     * @param $amount
     * @param $document_ref
     * @param $notes
     * @return bool
     */
    public function credit($amount,$document_ref, $notes)
    {
       return Account::credit($this->id, $this->getAccountType(), $amount, $document_ref, $notes);
    }

    /**
     * @param $amount
     * @param $document_ref
     * @param $notes
     * @return bool
     */
    public function debit($amount,$document_ref, $notes)
    {
        return Account::debit($this->id, $this->getAccountType(), $amount, $document_ref,$notes);
    }

}
