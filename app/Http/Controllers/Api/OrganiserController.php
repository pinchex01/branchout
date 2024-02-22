<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Organiser;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganiserApplicationRequest;

class OrganiserController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, Organiser::$rules);
        $type =  $request->input('type');

        $organiser = null;
        \DB::transaction(function() use ($type, $request, &$organiser){
            $user = $request->user();
            $organiser = Organiser::add_merchant($type, $request->all(), $user);
            //add some extra features here like fire events

            //add user as director
            $director_role  = Role::get_merchant_admin();
            $organiser->add_user($user,$director_role->id);
        });

        return response()->json($organiser, 200);
    }

    public function createOrganiserApplication(OrganiserApplicationRequest $request)
    {
        //ensure bank_id exists is type is bank
        if ($request->input('bank_account.account_type') == 'bank'){
            $this->validate($request, [
                'bank_account.bank_id' => "required|exists:banks,id"
            ]);
        }

        //all is good, proceed and create application
        $application = null;
        \DB::transaction(function() use ($request, &$application){
            $user = $request->user();
            $organiser_info  = Application::get_organiser_application_info_from_request($request);
            $bank_account_info = Application::get_bank_account_info_from_request($request);
            $payload  = $organiser_info + $bank_account_info;
            
            //create application
            $application  = Application::create_application($user,'organiser', $request->input('organiser.name'),"Organiser account application", $payload, $user, 'pending');
        });

        return response()->json($application, 200);
    }

    public function createSalesAgentApplication(OrganiserApplicationRequest $request)
    {
        //ensure bank_id exists is type is bank
        if ($request->input('bank_account.account_type') == 'bank'){
            $this->validate($request, [
                'bank_account.bank_id' => "required|exists:banks,id"
            ]);
        }

        //all is good, proceed and create application

        $application = null;
        \DB::transaction(function() use ($request, &$application){
            $user = $request->user();
            $organiser_info  = Application::get_sales_agent_application_info_from_request($request);
            $bank_account_info = Application::get_bank_account_info_from_request($request);
            $payload  = $organiser_info + $bank_account_info;
            
            //create application
            $application  = Application::create_application($user,'sales-agent', $request->input('organiser.name'),"Organiser account application", $payload, $user, 'pending');
        });

        return response()->json($application, 200);
    }


}
