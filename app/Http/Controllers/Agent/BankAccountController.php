<?php

namespace App\Http\Controllers\Agent;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Ledger;
use App\Models\Organiser;
use App\Models\Settlement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankAccountController extends Controller
{
    public function index(Organiser $organiser, Request $request)
    {
        $banks = Bank::all();

        $bank_accounts  = $organiser->banks()->with(['account','bank'])->paginate(20);

        \JavaScript::put([
            'Banks' => $banks,
        ]);

        $data  = [
            'organiser' => $organiser,
            'banks' => $banks,
            'bank_accounts' => $bank_accounts
        ];

        return view('agent.bank-accounts.index',$data)
            ->with('page_title', "Accounts");
    }

    public function show(Organiser $organiser, BankAccount $bankAccount, Request $request)
    {
        $settlements = $bankAccount->settlements()
            ->latest('settlements.created_at')
            ->paginate(20);

        $data  = [
            'organiser' => $organiser,
            'bank_account' => $bankAccount,
            'settlements' => $settlements,
            'summary' => $this->get_bank_account_summary($bankAccount),
        ];

        return view('agent.bank-accounts.view', $data)
            ->with("page_title", "Bank Account Details");
    }

    public function statements(Organiser $organiser, BankAccount $bankAccount, Request $request)
    {
        $account = $bankAccount->getAccount();

        $summary = $this->get_bank_account_summary($bankAccount);

        $ledgers = Ledger::selectRaw("
                    ledgers.id as id, ledgers.notes as notes, ledgers.credit as credit, ledgers.debit as debit,
                    ledgers.balance as balance, ledgers.txn_date as txn_date, ledgers.ref as ref
                ")
            ->where('ledgers.account_id',$account->id);

        if ($request->method() == "POST"){
            //filter records based ont he passed dates
            $this->validate($request,[
                'start'=>'required|date',
                'end'=>'required|date|after:start'
            ],[
                "start.required" => "Please specify the start date",
                'start.date'=>"Start/From must be a validate date",
                "end.required" => "Please specify the end date",
                'end.date'=>"To: must be a validate date"
            ]);

            $start = $request->get('start');
            $end = $request->get('end');

            $ledgers->whereRaw("DATE(ledgers.created_at) >= '{$start}' and DATE(ledgers.created_at) <= '{$end}' ");
        }else{
            //show the last 20 transactions
            $ledgers->take('20');
        }

        $items = $ledgers->latest('ledgers.txn_date')
            ->paginate(20);

        $this->flashInput($request->all());
        return view('agent.bank-accounts.statements',[
            'organiser' => $organiser,
            'bank_account' => $bankAccount,
            'summary' => $summary,
            'items' => $items
        ])->with('page_title','Bank Account Statements | Manage Banks');
    }

    public function withdraw(Organiser $organiser, BankAccount $bankAccount, Request $request)
    {
        if($request->method() == 'GET'){
            return redirect()->route('organiser.bank-accounts.view',[$organiser->slug, $bankAccount->id]);
        }

        $bankAccount->fresh(['account']);
        $user  = user();

        $account = $bankAccount->getAccount();
        //ensure balance is greater than minimum withdrawable amount
        $min_withdrawable = settings('min_withdrawable', 100);
        if ($account->getBalance() < $min_withdrawable){
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "Insufficient funds for withdrawal. The minimum amount you can withdraw is {$min_withdrawable}."]
                ]);
        }

        //todo: otp here

        $notes = "Settlement generated on ".date('Y-m-d')." by {$user} from IP: {$request->ip()}";
        $settlement = Settlement::create_new_settlement($bankAccount->id, 'Bank',$account->getBalance(),
            $notes, $bankAccount->account_no, $bankAccount->name, $bankAccount->bank);
        if ($settlement){
            $this->dispatch(new \App\Jobs\Settlement($settlement));

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Settlement request was successfully generated. Request may take a longer to complete. Please wait."]
                ]);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'danger', 'message' => "Sorry an error occurred while processing your request. Please try again."]
            ]);
    }

    private function get_bank_account_summary(BankAccount $bankAccount)
    {
        $account  = $bankAccount->getAccount();

        return [
            'total_credit' => $account->credit,
            'total_debit' => $account->debit,
            'total_balance' => $account->balance
        ];
    }
}
