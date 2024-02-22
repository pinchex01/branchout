<?php

namespace App\Http\Controllers\Agent;

use App\Models\Account;
use App\Models\Event;
use App\Models\Organiser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentController extends Controller
{
    public function dashboard(Organiser $organiser)
    {
        $events = Event::query()
            ->selectRaw("events.id, events.name as name, events.slug, events.start_date,
             events.end_date as end, events.location, sales_people.code as code")
            ->join('sales_people','sales_people.event_id','=','events.id')
            ->whereRaw("sales_people.organiser_id = ?",[$organiser->id])
            ->latest('events.created_at')
            ->get();

        $subscribed_events_ids =$organiser->sales()->selectRaw('event_id')->get()->pluck('event_id')->toArray();

        $upcoming_events = Event::query()
            ->selectRaw("events.id, events.name as name, events.slug, events.start_date,
             events.end_date as end, events.location")
             ->whereNotIn('events.id', $subscribed_events_ids)
            ->latest('events.created_at')
            ->get();

        $total_events  = $events->count();

        $sales_info  = $organiser->sales()
            ->selectRaw("sum(tickets_sold) as tickets, sum(fees) as commission, sum(total) as sales")
            ->first();

        $my_money  = Account::getOrCreate($organiser);

        $orders = $organiser->orders()
            ->with(['organiser'])->
            latest('orders.created_at')->take(5)->get();

        return view('agent.dashboard',[
            'organiser' => $organiser,
            'events' => $events,
            'orders' => $orders,
            'sales_info' => $sales_info,
            'my_money' => $my_money,
            'total_events' => $total_events,
            'upcoming_events' => $upcoming_events
        ])->with('page_title', "My Dashboard");
    }
}
