<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankAccount;
use App\Models\Ledger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $bank_accounts = BankAccount::query()
            ->with(['bank'])
            ->paginate(20);

        $data  = [
            'bank_accounts' => $bank_accounts
        ];

        return view('admin.bank-accounts.index', $data)
            ->with("page_title", "Manage Bank Accounts");
    }

    public function show(BankAccount $bankAccount, Request $request)
    {
        $settlements = $bankAccount->settlements()
            ->latest('settlements.created_at')
            ->paginate(20);

        $data  = [
            'bank_account' => $bankAccount,
            'settlements' => $settlements,
            'summary' => $this->get_bank_account_summary($bankAccount),
        ];

        return view('admin.bank-accounts.view', $data)
            ->with("page_title", "Bank Account Details");
    }

    public function statements(BankAccount $bankAccount, Request $request)
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
        return view('admin.bank-accounts.statements',[
            'bank_account' => $bankAccount,
            'summary' => $summary,
            'items' => $items
        ])->with('page_title','Bank Account Statements | Manage Banks');
    }

    private function get_bank_account_summary(BankAccount $bankAccount)
    {
        $account  = $bankAccount->getAccount();

        return [
            'total_credit' => $account->creditt,
            'total_debit' => $account->debitt,
            'total_balance' => $account->balance
        ];
    }

}
