<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\Settlement;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Ledger;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->can('create-banks');

        $available_banks = Bank::all()->pluck('name', 'id')->toArray();

        set_sql_mode();
        $banks = Bank::with(['accounts'])->selectRaw("banks.id, banks.name, banks.paybill, banks.status")
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.banks.index', [
            'banks' => $banks,
            'available_banks' => $available_banks
        ])->with('page_title', 'Manage Banks');
    }


    public function store(Request $request)
    {
        $this->can('create-banks');

        $this->validate($request, [
            'bank.name' => 'required|unique:banks,name',
            'bank.paybill' => 'required|integer'
        ]);

        $bank = new Bank([
            'name' => $request->input('bank.name'),
            'paybill' => $request->input('bank.paybill'),
            'status' => $request->input('bank.status'),
        ]);

        $bank->save();

        //log activity
        $user = user();
        activity('feed')->log("$user created system bank: {$bank}")
            ->causedBy($user);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Record saved successfully"]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Bank $bank
     * @param Request $request
     * @return \Illuminate\Http\Response|View
     */
    public function show(Bank $bank, Request $request)
    {
        $this->can('update-banks');

        $bank->load(['accounts']);
        $available_banks = Bank::all()->pluck('name', 'id')->toArray();

        return view('admin.banks.view', [
            'bank' => $bank,
            'available_banks' => $available_banks
        ])->with('page_title', 'Edit - Manage Banks');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Bank $bank
     * @return \Illuminate\Http\Response|View
     */
    public function edit(Bank $bank)
    {
        $this->can('update-banks');

        $available_banks = Bank::all()->pluck('name', 'id')->toArray();

        return view('admin.banks.edit_bank_account', [
            'available_banks' => $available_banks,
            'bank' => $bank
        ])->with('page_title', "Edit Bank Account ");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Bank $bank
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|View
     */
    public function update(Bank $bank, Request $request)
    {
        $this->can('update-banks');

        $this->validate($request, [
            'bank.name' => [
                'required',
                Rule::unique('banks', 'name')->ignore($bank->id)
            ],
            'bank.paybill' => 'required|integer'
        ]);

        $bank->fill([
            'name' => $request->input('bank.name'),
            'paybill' => $request->input('bank.paybill'),
            'status' => $request->input('bank.status'),
        ]);
        $bank->save();

        //log activity
        $user = user();
        activity('feed')->log("$user updated system bank #{$bank->id}({$bank})")
            ->causedBy($user);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Record updated successfully"]
            ]);
    }

    public function adminBanks(Request $request)
    {
        $banks = BankAccount::with(['bank'])
            ->systemBankAccounts()
            ->paginate(20)
            ->appends($request->except('page'));

        $available_banks = Bank::all()->pluck('name', 'id')->toArray();

        $collection_this_month = Ledger::join('accounts', 'accounts.id', '=', 'ledgers.account_id')
            ->join('bank_accounts', 'bank_accounts.id', '=', 'accounts.owner_id')
            ->where('accounts.type', Account::AC_BANK)
            ->where('bank_accounts.type', BankAccount::TYPE_SYSTEM)
            ->selectRaw("sum(ledgers.credit) as credit, sum(ledgers.debit) as debit, sum(ledgers.balance) as balance")
            ->whereRaw(" month(ledgers.created_at) = month(now())")
            ->whereRaw(" year(ledgers.created_at)  = year(now())")
            ->first();

        $collection_this_year = Ledger::join('accounts', 'accounts.id', '=', 'ledgers.account_id')
            ->join('bank_accounts', 'bank_accounts.id', '=', 'accounts.owner_id')
            ->where('accounts.type', Account::AC_BANK)
            ->where('bank_accounts.type', BankAccount::TYPE_SYSTEM)
            ->selectRaw("sum(ledgers.credit) as credit, sum(ledgers.debit) as debit, sum(ledgers.balance) as balance")
            ->whereRaw(" year(ledgers.created_at)  = year(now())")
            ->first();

        $collection_alltime = Ledger::join('accounts', 'accounts.id', '=', 'ledgers.account_id')
            ->join('bank_accounts', 'bank_accounts.id', '=', 'accounts.owner_id')
            ->where('accounts.type', Account::AC_BANK)
            ->where('bank_accounts.type', BankAccount::TYPE_SYSTEM)
            ->selectRaw("sum(ledgers.credit) as credit, sum(ledgers.debit) as debit, sum(ledgers.balance) as balance")
            ->first();

        $collection_summary = [
            'month' => $collection_this_month->credit,
            'year' => $collection_this_year->credit,
            'alltime' => $collection_alltime->credit
        ];

        return view('admin.banks.admin_banks', [
            'banks' => $banks,
            'available_banks' => $available_banks,
            'collection_summary' => collect($collection_summary)
        ])
            ->with('page_title', "Admin Bank Accounts ");
    }

    public function createAdminBankForm(Request $request)
    {
        $this->can('create-banks');

        $otp = $this->otp($request, trans('otp.admin.add_bank'));
        if ($otp) {
            return $otp;
        }

        $available_banks = Bank::all()->pluck('name', 'id')->toArray();

        return view('admin.banks.new_admin_bank', [
            'available_banks' => $available_banks
        ])->with("page_title", "Add Bank Account");
    }

    public function addAdminBankAccount(Request $request)
    {
        $this->can('create-banks');

        $this->validate($request, [
            'bank.account_type' => "required|in:bank,paybill",
            'bank.account_no' => [
                'required', 'confirmed', 'numeric', "unique:bank_accounts,account_no"
            ],
            'bank.bank_id' => 'required_if:bank.account_type,bank|exists:banks,id',
            'bank.settlement_schedule' => "required|in:on-demand,real-time"
        ], [
            'bank.account_type.in' => "Invalid account type selected",
            'bank.settlement_schedule.in' => "Invalid settlement schedule type selected"
        ]);

        $bank = BankAccount::createFromRequest($request, 0, BankAccount::TYPE_SYSTEM);
        $bank->status = 'active';
        $bank->save();

        return redirect()->route('admin.banks.index')
            ->with('alerts', [
                ['type' => 'success', 'message' => "Bank account {$bank->account_no} successfully added"]
            ]);
    }

    public function viewAdminBankAccount(BankAccount $bankAccount, Request $request)
    {
        $this->can('view-banks');

        $settlements = $bankAccount->settlements()
            ->latest('settlements.created_at');

        if ($request->method() == 'POST') {
            //filter records based ont he passed dates
            $this->validate($request, [
                'start' => 'required|date',
                'end' => 'required|date|after:start'
            ], [
                "start.required" => "Please specify the start date",
                'start.date' => "Start/From must be a validate date",
                "end.required" => "Please specify the end date",
                'end.date' => "To: must be a validate date"
            ]);

            $start = $request->get('start');
            $end = $request->get('end');

            $settlements->whereRaw("DATE(settlements.created_at) >= '{$start}' and DATE(settlements.created_at) <= '{$end}' ");
        } else {
            $settlements->take(20);
        }

        $items = $settlements->latest('settlements.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        $account = $bankAccount->getAccount();

        return view('admin.banks.view_admin_bank', [
            'bank' => $bankAccount,
            'settlements' => $items,
            'summary' => $account,
        ])->with('page_title', 'Bank Account Settlements | My Banks');

    }

    public function viewAdminBankAccountStatements(BankAccount $bankAccount, Request $request)
    {
        $this->can('view-banks');

        $account = $bankAccount->getAccount();

        $ledgers = Ledger::selectRaw("
                    ledgers.id as id, ledgers.notes as notes, ledgers.credit as credit, ledgers.debit as debit,
                    ledgers.balance as balance, ledgers.created_at as txn_date
                ")
            ->where('ledgers.account_id', $account->id);

        if ($request->method() == 'POST') {
            //filter records based ont he passed dates
            $this->validate($request, [
                'start' => 'required|date',
                'end' => 'required|date|after:start'
            ], [
                "start.required" => "Please specify the start date",
                'start.date' => "Start/From must be a validate date",
                "end.required" => "Please specify the end date",
                'end.date' => "To: must be a validate date"
            ]);

            $start = $request->get('start');
            $end = $request->get('end');

            $ledgers->whereRaw("DATE(ledgers.created_at) >= '{$start}' and DATE(ledgers.created_at) <= '{$end}' ");
        } else {
            $ledgers->take(20);
        }
        $items = $ledgers
            ->latest('ledgers.created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('admin.banks.view_admin_bank_statements', [
            'bank' => $bankAccount,
            'items' => $items,
            'summary' => $account,
        ])->with('page_title', 'Bank Account Settlements | My Banks');

    }


    public function updateAdminBankAccount(BankAccount $bankAccount, Request $request)
    {
        if ($request->method() == 'GET') {
            return redirect()->route('admin.banks.view', $bankAccount->id);
        }

        $this->can('update-banks');

        $this->validate($request, [
            'bank.settlement_schedule' => "required|in:on-demand,real-time"
        ], [
            'bank.settlement_schedule.in' => "Invalid settlement schedule type selected"
        ]);

        $type = $request->input('bank.account_type');

        $otp = $this->otp($request, trans('otp.admin.update_bank', [
            'account_name' => $bankAccount->name,
            'account_no' => $bankAccount->account_no
        ]));
        if ($otp) {
            return $otp;
        }

        \DB::transaction(function () use ($request, &$bankAccount, $type) {

            $bankAccount->fill($request->only(['bank.settlement_schedule'])['bank']);


            //check if settlement was changed
            if ($bankAccount->getOriginal('settlement_schedule') != $bankAccount->settlement_schedule) {
                //update currently running services to the new value
                $auto_settle = $bankAccount->settlement_schedule == 'real-time' ? 1 : 0;
                $bankAccount->services()->where('services.bank_account_id', $bankAccount->id)
                    ->update(['auto_settle' => $auto_settle]);
            }

            $bankAccount->save();

            $user = user();
            activity('feed')->log("$user updated bank account: {$bankAccount->id}")
                ->causedBy($user);
        });

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Bank account {$bankAccount->account_no} updated successfully"]
            ]);
    }

    public function toggleDefaultBank(BankAccount $bankAccount, Request $request)
    {
        if ($request->method() == 'GET') {
            return redirect()->route('admin.banks.index');
        }

        $this->can(['update-banks']);

        $otp = $this->otp($request, trans('otp.admin.toggle_default_bank', [
            'account_name' => $bankAccount->name,
            'account_no' => $bankAccount->account_no
        ]));
        if ($otp) {
            return $otp;
        }

        //remove the current default bank account
        BankAccount::systemBankAccounts()->update(['is_default' => 0]);

        //update the selected account to default
        $bankAccount->is_default = 1;
        $bankAccount->save();

        $user = user();
        activity('feed')->log("$user changed default bank account for admin to: {$bankAccount}")
            ->causedBy($user);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Bank account {$bankAccount->account_no} is now set as the default"]
            ]);

    }


    public function withdraw(BankAccount $bankAccount, Request $request)
    {
        $this->can('create-settlements');

        if ($request->method() == 'GET') {
            return redirect()->route('admin.banks.view', [$bankAccount->id]);
        }

        $account = $bankAccount->getAccount();

        //ensure balance is greater than minimum withdrawable amount
        $min_withdrawable = settings('min_withdrawable', 100);
        if ($account->getBalance() < $min_withdrawable) {
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "Insufficient funds for withdrawal. The minimum amount you can withdraw is {$min_withdrawable}."]
                ]);
        }

        $user = user();

        $otp = $this->otp($request, trans('otp.admin.withdrawal', [
            'account_name' => $bankAccount->name,
            'account_no' => $bankAccount->account_no
        ]));
        if ($otp) {
            return $otp;
        }

        //check if there is money in the account
        if ($account->balance > 0) {
            $this->dispatchNow(new Settlement($bankAccount, $account->balance, "Manual withdrawal by {$user} from IP: {$request->ip()}"));
            activity('feed')->log("$user triggered withdrawal of amount KES {$account->balance} from : {$bankAccount}")
                ->causedBy($user);

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Settlement requested was generated successfully!"]
                ]);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'danger', 'message' => "Insufficient account balance"]
            ]);

    }
}
