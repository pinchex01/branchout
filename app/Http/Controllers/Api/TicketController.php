<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $this->validateCreate($request);

        $event_id = $request->input('event_id');

        $ticket =  null;
        \DB::transaction(function () use (&$ticket, $request, $event_id){
            $ticket =  Ticket::add_ticket($event_id, $request->all());
        });

        return response()->json($ticket, 200);
    }

    public function details(Ticket $ticket, Request $request)
    {
        //todo: validate that user can view

        return response()->json($ticket, 200);
    }

    public function update(Ticket $ticket, Request $request)
    {
        $this->validate($request,[
            'name' => [
                "required",
                Rule::unique('tickets','name')
                    ->where('event_id',$request->input('event_id'))
                ->ignore($ticket->id)
            ],
            'description' => "required",
            'min_per_person' => "required|integer|min:1",
            'max_per_person' => "required|integer|greater_than_field:min_per_person",
            "on_sale_date" => "required|date",
            "end_sale_date" => "required|date|after:on_sale_date",
            'price' => 'required|numeric|min:0'
        ]);

        \DB::transaction(function () use (&$ticket, $request){
            $ticket =  $ticket->fill(map_props_to_params($request->all(), $ticket->getFillable()));
            $ticket->save();
        });

        return response()->json($ticket, 200);
    }

    public function ticket_available(Request $request)
    {
        $this->validate($request, [
            'ticket_id' => "required|exists:tickets,id"
        ]);

    }

    protected function validateCreate(Request $request)
    {
        $this->validate($request,[
            'event_id' => "required|exists:events,id",
            'name' => [
                "required",
                Rule::unique('tickets','name')
                ->where('event_id',$request->input('event_id'))
            ],
            'description' => "required",
            'min_per_person' => "required|integer|min:1",
            'max_per_person' => "required|integer|greater_than_field:min_per_person",
            "on_sale_date" => "required|date|after:yesterday",
            "end_sale_date" => "required|date|after:on_sale_date",
            'price' => 'required|numeric|min:0'
        ]);
    }
}
