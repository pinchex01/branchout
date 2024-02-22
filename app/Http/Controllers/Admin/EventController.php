<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()->latest('events.created_at')->paginate(20);

        return view('admin.events.index',[
            'events' => $events
        ])->with('page_title', "Events ");
    }

    public function show(Event $event, Request $request)
    {
        $event->load(['account','orders','orders.order_items']);
        $chart_data = $this->get_charts_data($event);

        $today  = Carbon::now();
        $days_ago  = $today->subDays(20);

        \JavaScript::put([
            'EventChartData' => $chart_data
        ]);

        return view('admin.events.view',[
            'event' => $event,
            'event_menu' => true
        ])->with('page_title',"Manage Event");
    }

    public function get_charts_data(Event $event, $days = 20)
    {
        $today  = Carbon::now();
        $days_ago  = $today->subDays($days);

        $order_items  = $event->orders()
            ->selectRaw("DATE(orders.order_date) as order_date, order_items.ticket_id, order_items.name, order_items.quantity, order_items.total")
            ->join('order_items','order_items.order_id','=','orders.id')
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
