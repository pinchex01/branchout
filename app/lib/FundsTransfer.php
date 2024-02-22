<?php
/**
 * Created by PhpStorm.
 * User: mitac
 * Date: 5/9/2017
 * Time: 10:32 PM
 */

namespace App\Lib;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Event;
use App\Models\Ledger;
use App\Models\Order;
use App\Models\Organiser;
use App\Models\Payment;
use App\Models\SalesPerson;
use App\Models\Tariff;
use App\Models\User;

/**
 * Class FundsTransfer
 * @package App\Lib
 */
class FundsTransfer
{

    public function process_payment(Order $order, Payment $payment, SalesPerson $salesPerson = null)
    {
        $amount =  $payment->total;

        //if tickets were free
        if (!$amount)
            return [$amount, $commission = 0];

        $order->load(['user','event','event.organiser']);

        $event  =  $order->event;
        $organiser  = $event->organiser;
        $bank_account =  $event->get_destination_bank_account();
        \DB::beginTransaction();

        $notes = "Payment of KES {$amount} received for Order No. {$order->ref_no}";

        #if payment was made from wallet, debit the the users wallet
        if($payment->channel  == 'wallet')
          $this->post_transaction_to_user('debit', $order->user, $amount, $order->ref_no, $notes);


        //credit the general ledger account;
        $general_ledger = Account::get_general_ledger_account();
        Account::credit($general_ledger->owner_id, $general_ledger->owner_type,$amount,$order->ref_no, $notes);

        //credit event with the payment
        $event->credit($amount, $order->ref_no, $notes);

        //credit organiser account
        $organiser->credit($amount,$order->ref_no, $notes);

        $commission = 0;
        $tickets_sold = $order->order_items()->sum('quantity');
        //check if purchased through as sales person and split payment
        if($order->sales_person_id && $salesPerson && $commission = Tariff::get_sales_agent_commission($amount, $tickets_sold, $salesPerson)){
            //post commission to the sales person
            $this->post_sales_person_commission($salesPerson,$order, $amount,$commission);

            //post amount less commission to the organisers bank account
            $this->post_payment_to_bank_account('credit',$bank_account, $amount - $commission, $order->ref_no, $notes);
        }else{
            //post full amount to the organisers bank account
            $this->post_payment_to_bank_account('credit',$bank_account, $amount, $order->ref_no, $notes);
        }
        \DB::commit();

        return [$amount, $commission];
    }

    public function cancel_order(Order $order)
    {
        #if order was free, do nothing
        if(!$order->amount || !$order->is_complete())
            return [null, null];

        $order->load(['event','event.organiser','event.organiser.account']);
        $event = $order->event;
        $organiser =  $event->organiser;
        $salesPerson = $order->sales_person;
        $amount =  $order->amount;
        $notes = "Order #{$order->ref_no} for {$order->event} cancelled.";

        \DB::beginTransaction(); //begin transaction
        #debit general ledgers account
        $general_ledger = Account::get_general_ledger_account();
        Account::debit($general_ledger->owner_id, $general_ledger->owner_type,$amount,$order->ref_no, $notes);

        #debit event
        $event->debit($amount, $order->ref_no, $notes);

        #debit organiser account
        $organiser->debit($amount,$order->ref_no, $notes);

        $commission = 0;
        $bank_account =  $event->get_destination_bank_account();
        #check if order was purchase through a sales person and debit commission that was given
        if($salesPerson){
            //reverse commission awarded
            $commission = $this->reverse_sales_person_commission($salesPerson,$order);

            //post amount less commission to the organisers bank account
            $this->post_payment_to_bank_account('debit',$bank_account, $amount - $commission, $order->ref_no, $notes);
        }else{
            //post full amount to the organisers bank account
            $this->post_payment_to_bank_account('debit',$bank_account, $amount, $order->ref_no, $notes);
        }

        \DB::commit(); //end transaction

        return [$amount, $commission];
    }

    public function post_payment_to_bank_account($transaction_type, BankAccount $bankAccount, $amount, $document_ref, $notes)
    {
        return $bankAccount->$transaction_type($amount,$document_ref, $notes);
    }

    private function post_sales_person_commission(SalesPerson $salesPerson, Order $order, $total_paid, $commission)
    {
        if(!$salesPerson)
            return;

        \DB::beginTransaction();
        $sales_agent = $salesPerson->organiser;
        $agent_bank_account = BankAccount::get_default_for_organiser($sales_agent);

        //credit sales agent general account
        $sales_agent->credit($commission, $order->ref_no, "Commission from payment received for Order No. {$order->ref_no}");

        //credit sales agent bank_account if added
        if (!$agent_bank_account){
            $agent_bank_account->credit($commission, $order->ref_no, "Commission from payment received for Order No. {$order->ref_no}");
        }
        \DB::commit();
    }

    public function reverse_sales_person_commission(SalesPerson $salesPerson, Order $order)
    {
        $commission = 0;
        if(!$salesPerson)
            return $commission;

        \DB::beginTransaction();
        $sales_agent = $salesPerson->organiser;
        $agent_bank_account = BankAccount::get_default_for_organiser($sales_agent);

        #get commission that was awarded for this order
        $commission = Ledger::query()->where([ 'account_id' => $agent_bank_account->account->id, 'ref' => $order->ref_no])->sum('credit');
        $sales_agent->debit($commission, $order->ref_no, "Order cancelled. Commission reversal from payment received for Order No. {$order->ref_no}");

        //credit sales agent bank_account if added
        if (!$agent_bank_account){
            $agent_bank_account->debit($commission, $order->ref_no, "Order cancelled. Commission reversal from payment received for Order No. {$order->ref_no}");
        }
        \DB::commit();

        return $commission;
    }

    public function post_transaction_to_user($type, User $user, $amount, $document_ref, $notes)
    {
        return $user->$type($amount, $document_ref, $notes);
    }


    public function withdraw_from_bank_account(BankAccount $bankAccount, $amount)
    {
        $bankAccount->load(['owner','owner.account','account']);
    }
}
