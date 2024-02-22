<?php

namespace App\Models;

use App\AccountableInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const AC_SUSPENSE = 'Suspense';
    const AC_USER = 'User';
    const AC_ORGANISER = 'Organiser';
    const AC_APP = 'System';
    const AC_EVENT = 'Event';
    const AC_BANK = 'Bank';

    protected $table = 'accounts';

    protected $fillable = [
        'owner_id', 'owner_type', 'name', 'credit', 'debit', 'balance'
    ];

    public static $suspense_account_id = 1;

    /**
     * Create account from an AccountableInterface
     *
     * @param AccountableInterface $accountable
     *
     * @return Account
     */
    public static function getOrCreate(AccountableInterface $accountable)
    {
        $ac = Account::whereOwnerId($accountable->getAccountId())
            ->whereOwnerType($accountable->getAccountType())->first();

        if (!$ac) {
            $ac = new Account([
                'owner_id' => $accountable->getAccountId(),
                'owner_type' => $accountable->getAccountType(),
                'name' => $accountable->getAccountName(),
                'credit' => 0,
                'debit' => 0
            ]);

            $ac->save();

            //todo: hook any account created event here
        }

        return $ac;
    }

    /**
     * Get GLA
     * @return Account
     */
    public static function get_general_ledger_account()
    {
        return self::where([
            'owner_id' => 1,
            'owner_type' => 'Suspense'
        ])->first();
    }

    /**
     * Get withdrawal  suspense account
     *
     * @return Account
     */
    public static function get_withdrawal_suspense_account()
    {
        return self::where([
            'owner_id' => 2,
            'owner_type' => 'Suspense'
        ])->first();
    }

    /**
     * @param $owner_id
     * @param $owner_type
     * @return Account|null
     */
    public static function get($owner_id, $owner_type)
    {
        return Account::where([
            'owner_id' => $owner_id,
            'owner_type' => $owner_type
        ])->firstOrFail();
    }

    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Get all ledger entries related to particular account
     */
    public function ledgers()
    {
        return $this->hasMany(Ledger::class, 'account_id', 'id');
    }

    /**
     * Make a credit transaction into an account
     * If transaction went through return true
     *
     * @param $owner_id
     * @param $owner_type
     * @param $amount
     * @param $document_ref
     * @param $notes
     * @return bool $result
     */
    public static function credit($owner_id, $owner_type, $amount, $document_ref, $notes)
    {
        $result = false;
        \DB::transaction(function () use ($owner_id, $document_ref, $owner_type, $amount, $notes, &$result) {
            /* Get account and credit with the amount */
            $account = self::get($owner_id, $owner_type);
            $account->credit += $amount;
            $account->balance  = $account->credit - $account->debit;
            $account->save();

            /* Insert ledger entry */
            \DB::table("ledgers")->insert([
                "account_id" => $account->id,
                "credit" => $amount,
                "debit" => 0,
                "balance" => $account->balance + $amount,
                "notes" => $notes,
                'ref' => $document_ref,
                'txn_date' => Carbon::now()
            ]);

        });

        return $result;
    }

    /**
     * Make a debit transaction into an account
     * If transaction went through return true
     *
     * @param $owner_id
     * @param $owner_type
     * @param $amount
     * @param $document_ref
     * @param $notes
     * @return bool $result
     */
    public static function debit($owner_id, $owner_type, $amount, $document_ref, $notes)
    {
        $result = false;
        \DB::transaction(function () use ($owner_id, $owner_type, $document_ref, $amount, $notes, &$result) {
            /* Get account and credit with the amount */
            $account = self::get($owner_id, $owner_type);
            $account->debit += $amount;
            $account->balance  = $account->credit - $account->debit;
            $account->save();

            /* Insert ledger entry */
            \DB::table("ledgers")->insert([
                "account_id" => $account->id,
                "credit" => 0,
                "debit" => $amount,
                "balance" => $account->balance - $amount,
                "notes" => $notes,
                'ref' => $document_ref,
                'txn_date' => Carbon::now()
            ]);

            $result = true;
        });

        return $result;
    }

    /**
     * Post a credit or debit transaction
     *
     * @param $type
     * @param $owner_id
     * @param $owner_type
     * @param $amount
     * @param $document_ref
     * @param $notes
     * @return bool
     */
    public static function transact($type, $owner_id, $owner_type, $amount, $document_ref, $notes)
    {
        $success = false;
        switch ($type) {
            case 'credit':
                $success = Account::credit($owner_id, $owner_type, $amount, $document_ref,  $notes);
                break;
            case 'debit':
                $success = Account::debit($owner_id, $owner_type, $amount, $document_ref, $notes);
        }

        return $success;
    }

    /**
     * Transfer funds between two accounts
     *
     * @param array $accounts
     * @param $amount
     * @param $document_ref
     * @param $notes
     */
    public static function transfer(array $accounts, $amount, $document_ref, $notes)
    {
        //post transactions
        \DB::transaction(function () use ($accounts, $amount, $notes, $document_ref) {
            $credit = $accounts['credit'];
            $debit = $accounts['debit'];
            self::debit($debit['owner_id'], $debit['owner_type'], $amount, $document_ref, $notes);
            self::credit($credit['owner_id'], $credit['owner_type'], $amount, $document_ref, $notes);
        });
    }
}
