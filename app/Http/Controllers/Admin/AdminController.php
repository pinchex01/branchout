<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Order;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $events = Event::query()
            ->selectRaw("events.id, events.name as title, events.slug, events.start_date as start, events.end_date as end")
            ->get()
            ->toArray();

        //add url to my events
        $my_events  = array_map(function ($event) {
            return $event + [ 'url' => route('admin.events.view',[ $event['id']])];
        }, $events);

        $chart_data  = $this->get_charts_data(20);

        \JavaScript::put([
            'MyEvents' => $my_events,
            'SalesChartData' => $chart_data
        ]);

        return view('admin.dashboard',[
            'events' => $my_events
        ])->with('page_title', "My Dashboard");
    }

    public function get_charts_data($days = 20)
    {
        $today  = Carbon::now();
        $days_ago  = $today->subDays($days);

        $order_items  = Order::query()
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



        $data = [
            'chartData'  => $result,
        ];

        return $data;
    }
}
