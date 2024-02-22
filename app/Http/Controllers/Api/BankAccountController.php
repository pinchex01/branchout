<?php

namespace App\Http\Controllers\Api;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $owner_id = $request->input('owner_id');
        $owner_type = $request->input('owner_type');
        $this->validate($request, BankAccount::get_validation_rules($owner_id, $owner_type));

        $bank = BankAccount::create_from_attributes($request->all(), $owner_id, $owner_type);

        return response()->json($bank, 200);
    }
}
