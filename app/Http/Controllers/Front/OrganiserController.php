<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\BaseController;
use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganiserController extends Controller
{
    public function index(Request $request)
    {
        $user  =  user();
        //get all applications
        $applications  = $user->applications()->where('type','organiser')->get();

        //get all organisers
        $organisers  = $user->organisers()->get();
        $banks = Bank::all();

        \JavaScript::put([
            'Banks' => $banks,
        ]);

        $data = [
            'applications' => $applications,
            'organisers' => $organisers
        ];

        return view('user.organisers.index', $data)
            ->with('page_title',"Manage Organisers");
    }

    public function create(Request $request)
    {
        
        $banks = Bank::all();

        \JavaScript::put([
            'Banks' => $banks,
        ]);
        return view('user.organisers.new')
            ->with('page_title',"Add Organiser");
    }

    public function createSalesAgent(Request $request)
    {
        $user  =  user();

        $banks = Bank::all();

        //get all applications
        $applications  = $user->applications()->where('type','sales-agent')->get();

        //get all organisers
        $organisers  = $user->organisers()->get();

        $data = [
            'applications' => $applications,
            'organisers' => $organisers
        ];
        

        \JavaScript::put([
            'Banks' => $banks,
        ]);
        return view('user.organisers.sales-agent', $data)
            ->with('page_title',"Sales Agent");
    }
}
