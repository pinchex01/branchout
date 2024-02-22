<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function generalSettings()
    {
        return view('admin.settings.general')
            ->with('page_title','General Settings');
    }

    public function generalSettingsSave(Request $request)
    {
        $input  = $request->except('_token');

        settings($input);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Changes saved successfully"]
            ]);
    }

    public function paymentsSettings()
    {
        $admin_accounts = BankAccount::with(['bank'])
            ->systemBankAccounts()
            ->selectRaw("concat(bank_accounts.account_no,' - ', bank_accounts.name) as name, bank_accounts.id as bank_id")
            ->get()
            ->pluck('name', 'bank_id')
            ->toArray();

        return view('admin.settings.payments',[
            'admin_accounts' => $admin_accounts
        ])->with('page_title','Payment Settings -');
    }

    public function paymentsSettingsSave(Request $request)
    {
        $input  = $request->except('_token');

        settings($input);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Changes saved successfully"]
            ]);
    }

    public function settlementSettings()
    {
        return view('admin.settings.settlements')
            ->with('page_title','Settlements Settings -');
    }

    public function settlementSettingsSave(Request $request)
    {
        $input  = $request->except('_token');

        settings($input);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Changes saved successfully"]
            ]);
    }

    public function allSettings(Request $request)
    {
        if ($request->method() == 'POST'){

            $input  = $request->except('_token');

            settings($input);

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Changes saved successfully"]
                ]);
        }

        $settings = \Setting::all();

        return view('admin.settings.all',[
            'settings' => $settings,
        ])->with('page_title','All Settings -');

    }

    public function addSetting(Request $request)
    {
        $this->validate($request, [
            'key' => "required|unique:settings,key|alpha_dash",
        ]);

        $params = [
            $request->input('key') => $request->input('value')
        ];

        settings($params);

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Changes saved successfully"]
            ]);
    }

    public function updateSetting(Request $request)
    {
        $params = [
            $request->input('pk') => $request->input('value')
        ];

        settings($params);

        if ($request->ajax()){
            return response()->json([
                'status' => 'success',
                'msg' => "Change updated successfully"
            ]);
        }

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "Changes saved successfully"]
            ]);
    }

    public function ticketDesign(Request $request)
    {
        if ($request->method() == 'POST'){
            $this->validate($request, [
                'ticket_html' => "required"
            ]);

            settings($request->only(['ticket_html']));

            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'success', 'message' => "Changes saved successfully"]
                ]);
        }

        return view('admin.settings.design_ticket')
            ->with('page_title','Ticket Layout');
    }

    public function previewTicketDesign(Request $request)
    {
        $ticket_html  = settings('ticket_html');

        return view('blank', [
          'html' => $ticket_html
        ])->with('page_title','Ticket Layout');
    }
}
