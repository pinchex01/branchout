<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendee;
use App\Models\Organiser;
use App\Models\Event;
use App\Models\User;
use App\Models\Order;

class AttendeeController extends Controller
{
    public function orgListAttendees(Organiser $organiser, Event $event, Request $request)
    {
        $attendees  = Attendee::filter($request)
                ->with(['event','ticket','order','user'])
                ->where('event_id', $event->id)
                ->complete()
                ->latest('attendees.created_at')
                ->get();
        
        return response()->json($attendees, 200);
    }

    public function orgGetTicketInfo(Organiser $organiser, Event $event, $ticket_no)
    {
        $ticket = Attendee::get_by_ticket_no($ticket_no);

        if(!$ticket){
            return response()->json([
                'status' => 'fail',
                "message" => 'Ticket does not exist'
            ], 422);
        }

        $ticket->load(['event', 'order', 'ticket', 'user']);

        if(!$ticket->order->is_complete()){
            return response()->json([
                'status' => 'fail',
                "message" => 'Ticket does not exist'
            ], 422);
        }

        return response()->json($ticket, 200);
    }

    public function orgCheckInTicket(Organiser $organiser, Event $event, Request $request)
    {
        $this->validate($request, [
           'ticket_no' => "required|exists:attendees,ref_no"
        ]);

        $ticket_no = $request->get('ticket_no');

        $ticket = Attendee::get_by_ticket_no($ticket_no);
        $ticket->load(['event', 'order', 'ticket']);

        if($ticket->check_in_time){
            return response()->json([
                'status' => 'fail',
                "message" => 'Ticket has already been checked in'
            ], 422);
        }

        $ticket = $ticket->check_in($request->user());

        $ticket->load(['event', 'order', 'ticket', 'user']);

        return response()->json($ticket, 200);
    }
    
}
