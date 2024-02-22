<?php

namespace App\Http\Controllers\Admin;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Role;
use App\Models\Organiser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganiserController extends Controller
{
    public function index(Request $request)
    {
        $organisers = Organiser::query()
            ->paginate(20);

        $data = [
            'organisers' => $organisers
        ];

        return view('admin.organisers.index', $data)
            ->with("page_title", "Manage Organisers");
    }

    public function show(Organiser $organiser, Request $request)
    {
        $summary = $this->get_organiser_summary($organiser);
        $data = [
            'summary' => $summary,
            'organiser' => $organiser
        ];
        return view('admin.organisers.view',$data)
            ->with("page_title","Organiser Details");
    }

    public function events(Organiser $organiser, Request $request)
    {
        $summary = $this->get_organiser_summary($organiser);
        $events = $organiser->events()->paginate(20);
        $data = [
            'summary' => $summary,
            'organiser' => $organiser,
            'events' => $events
        ];
        return view('admin.organisers.events',$data)
            ->with("page_title","Organiser Details | Events");
    }

    public function bankAccounts(Organiser $organiser, Request $request)
    {
        $summary = $this->get_organiser_summary($organiser);
        $bank_accounts = $organiser->bank_accounts()->paginate(20);
        $banks = Bank::all();

        \JavaScript::put([
            'Banks' => $banks,
        ]);

        $data = [
            'summary' => $summary,
            'organiser' => $organiser,
            'bank_accounts' => $bank_accounts
        ];
        return view('admin.organisers.bank_accounts',$data)
            ->with("page_title","Organiser Details | Bank Accounts ");
    }

    public function users(Organiser $organiser, Request $request)
    {
        $summary = $this->get_organiser_summary($organiser);
        $users = $organiser->users()->with(['merchant_roles'=>function($query) use ($organiser){
            $query->where('merchant_users.merchant_id', $organiser->id);
        }])->paginate(20);
        
        $roles  = Role::merchantRoles()->selectRaw("roles.id as id, roles.display_name as name")
            ->get();

        \Javascript::put([
          'frmRoles' => $roles
        ]);

        $data = [
            'summary' => $summary,
            'organiser' => $organiser,
            'users' => $users,
            'roles' => $roles
        ];
        return view('admin.organisers.users',$data)
            ->with("page_title","Organiser Details | Users ");
    }

    private function get_organiser_summary(Organiser $organiser)
    {
        $events = $organiser->events()
            ->selectRaw("events.id, events.name as title, events.slug, events.start_date as start,
             events.end_date as end, events.location")
            ->get();

        $total_events  = $events->count();

        $tickets_sold  = $organiser->orders()
            ->join('order_items','order_items.order_id','=','orders.id')
            ->sum('order_items.quantity');

        $total_sales  = Account::getOrCreate($organiser)->credit;

        $orders = $organiser->orders()->latest('orders.created_at')->take(5)->get();

        //add url to my events
        $my_events  = array_map(function ($event) use($organiser){
            return $event + [ 'url' => route('organiser.events.view',[$organiser->slug, $event['id']])];
        }, $events->toArray());

        $summary  = collect([
            'tickets_sold' => $tickets_sold,
            'total_events' => $total_events,
            'total_sales' => $total_sales,
        ]);

        return $summary;
    }
}
