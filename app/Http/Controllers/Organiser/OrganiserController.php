<?php

namespace App\Http\Controllers\Organiser;

use App\Models\Account;
use App\Models\Organiser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganiserController extends Controller
{
    public function dashboard(Organiser $organiser)
    {
        $events = $organiser->events()
            ->selectRaw("events.id, events.name as title, events.slug, events.start_date as start,
             events.end_date as end, events.location, events.sales_volume, events.tickets_sold")
            ->get();

        $total_events  = $events->count();


        $orders = $organiser->orders()->latest('orders.created_at')->take(5)->get();

        //add url to my events
        $my_events  = array_map(function ($event) use($organiser){
            return $event + [ 'url' => route('organiser.events.view',[$organiser->slug, $event['id']])];
        }, $events->toArray());

        $summary  = collect([
           'tickets_sold' => $events->sum('sales_volume'),
            'total_events' => $total_events,
            'total_sales' => $events->sum('tickets_sold'),
        ]);

        \JavaScript::put([
            'MyEvents' => $my_events
        ]);

        return view('organiser.dashboard',[
          'organiser' => $organiser,
            'events' => $events,
            'orders' => $orders,
            'summary' => $summary
        ])->with('page_title', "My Dashboard");
    }
}
