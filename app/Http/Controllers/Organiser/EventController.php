<?php

namespace App\Http\Controllers\Organiser;

use App\Models\Event;
use App\Models\Organiser;
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
        $events = $organiser->events()->latest('events.created_at')->paginate(20);

        return view('organiser.events.index',[
            'organiser' => $organiser,
            'events' => $events
        ])->with('page_title', "Events ");
    }

    public function create(Organiser $organiser, Request $request)
    {
        $this->can('create-events');

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
        $this->can('view-events');
        $event->load(['account','orders','orders.order_items']);
        $chart_data = $this->get_charts_data($event);

        \JavaScript::put([
            'EventChartData' => $chart_data
        ]);

        return view('organiser.events.view',[
            'organiser' => $organiser,
            'event' => $event,
            'event_menu' => true
        ])->with('page_title',"Manage Event");
    }

    public function edit(Organiser $organiser, Event $event, Request $request)
    {
        $this->can('update-events');
        $bank_accounts = [];
        $organiser->bank_accounts()->with(['bank'])
            ->each(function ($account) use (&$bank_accounts){
                $bank_accounts[] =  ['id' => $account->id, 'name' => $account->full_account_name ];
            });

        \JavaScript::put([
            'OrganiserBankAccounts' => $bank_accounts
        ]);

        return view('organiser.events.edit',[
            'organiser' => $organiser,
            'event' => $event,
            'event_menu' => true,
            'bank_accounts' => $bank_accounts
        ])->with('page_title',"Manage Event");
    }


    public function listSalesPeople(Organiser $organiser, Event $event, Request $request)
    {
        $sales_persons  =  $event->sales_people()->with(['organiser'])->paginate(20);

        $organisers = Organiser::all();

        return view('organiser.sales_people.index',[
            'organiser' => $organiser,
            'event' => $event,
            'sales_people' => $sales_persons,
            'event_menu' => true,
            'organisers' => $organisers
        ])->with('page_title',"Manage Event");
    }

    public function get_charts_data(Event $event, $days = 20)
    {
        $today  = Carbon::now();
        $days_ago  = $today->subDays($days);

        $order_items  = $event->orders()
            ->selectRaw("DATE(orders.order_date) as order_date, order_items.ticket_id, order_items.name, order_items.quantity, order_items.total")
            ->join('order_items','order_items.order_id','orders.id')
            ->whereRaw("DATE(orders.order_date) > ?",[$days_ago->format('Y-m-d')])
            ->complete()
            ->get();

        //dd($order_items->toArray());

        $startDate = new DateTime("-$days days");
        $dates = new DatePeriod(
            $startDate, new DateInterval('P1D'), $days
        );

        /*
         * Iterate through each possible date, if no stats exist for this date set default values
         * Otherwise, if a date does exist use these values
         */
        $result = [];
        $tickets_data = [];
        foreach ($dates as $date){
          $tickets_sold = $order_items->sum(function($item) use ($date){
            return $item->order_date->format('Y-m-d') == $date->format('Y-m-d') ? $item->total : 0;
          });
          $sales_volume = $order_items->sum(function($item) use ($date){
            return $item->order_date->format('Y-m-d') == $date->format('Y-m-d') ? $item->quantity : 0;
          });
            $result[] = [
                'date'         => $date->format('Y-m-d'),
                'sales_volume' => $tickets_sold,
                'tickets_sold' => $sales_volume,
            ];
        }

        foreach ($event->tickets as $ticket) {
            $tickets_data[] = [
                'value' => $ticket->quantity_sold,
                'label' => $ticket->name,
            ];
        }

        $data = [
            'chartData'  => $result,
            'ticketData' => $tickets_data,
        ];

        return $data;
    }

}
