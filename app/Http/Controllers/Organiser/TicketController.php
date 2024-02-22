<?php

namespace App\Http\Controllers\Organiser;

use App\Models\Event;
use App\Models\Organiser;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TicketController extends Controller
{
    public function index(Organiser $organiser, Event $event, Request $request)
    {
        $tickets  = $event->tickets()
            ->latest('tickets.created_at')
            ->paginate(20);

        return view('organiser.tickets.index',[
            'organiser' => $organiser,
            'event' => $event,
            'tickets' => $tickets,
        ])->with('page_title',"Event Tickets");
    }

    public function edit(Organiser $organiser, Event $event, Ticket $ticket, Request $request)
    {
        $data  = [
            'organiser' => $organiser,
            'event' => $event,
            'ticket' => $ticket
        ];
        \JavaScript::put([
            'Ticket' => $ticket
        ]);
        return view('organiser.tickets.edit',$data);
    }
}
