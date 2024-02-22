<?php

namespace App\Http\Controllers\Agent;

use App\Models\Event;
use App\Models\Organiser;
use App\Models\SalesPerson;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Organiser $organiser, Request $request)
    {
        $events = Event::query()
            ->selectRaw("events.id, events.name as name, events.slug, events.start_date,
             events.end_date as end, events.location, events.status")
            ->join('sales_people','sales_people.event_id','=','events.id')
            ->whereRaw("sales_people.organiser_id = ?",[$organiser->id])
            ->latest('events.created_at')
            ->get();

        return view('agent.events.index',[
            'organiser' => $organiser,
            'events' => $events
        ])->with('page_title', "Events ");
    }

    public function browse(Organiser $organiser, Request $request)
    {
        $events = Event::query()
            ->with(['organiser'])
            ->status('published')
            ->isPrivate(0)
            ->latest('events.created_at')
            ->paginate(20);

        \JavaScript::put([
            'Events' => $events
        ]);

        return view('agent.events.browse',[
            'organiser' => $organiser,
            'events' => $events
        ])->with('page_title', "Find Events ");
    }

    public function create(Organiser $organiser, Request $request)
    {
        $bank_accounts = [];
        $organiser->bank_accounts()->with(['bank'])
            ->each(function ($account) use (&$bank_accounts){
                $bank_accounts[] =  ['id' => $account->id, 'name' => $account->full_account_name ];
            });

        \JavaScript::put([
            'OrganiserBankAccounts' => $bank_accounts
        ]);

        return view('organiser.events.new',[
            'organiser' => $organiser,
            'bank_accounts' => $bank_accounts
        ])->with('page_title', "Add Event ");
    }

    public function show(Organiser $organiser, Event $event, Request $request)
    {
        $event->load(['account','orders','orders.order_items']);

        return view('agent.events.view',[
            'organiser' => $organiser,
            'event' => $event,
            'event_menu' => true
        ])->with('page_title',"Manage Event");
    }

    public function becomeAgent(Organiser $organiser, Event $event, Request $request)
    {
        if($event->private)
            return redirect()->back()
                ->with('alerts', [
                    ['type' => 'danger', 'message' => "This is event: {$event} is private, you can only be added by the organiser"]
                ]);


        $sales_agent = SalesPerson::add_sales_person($event, $organiser, 'active');

        return redirect()->back()
            ->with('alerts', [
                ['type' => 'success', 'message' => "You've been successfully added as a sales agent to event {$event}"]
            ]);
    }

}
