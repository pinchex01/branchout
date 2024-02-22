<?php

namespace App\Http\Controllers\Organiser;

use App\Models\Event;
use App\Models\Organiser;
use App\Models\Attendee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendeeController extends Controller
{
    public function index(Organiser $organiser, Event $event, Request $request)
    {
        $builder  = Attendee::filter($request)
                ->where('event_id', $event->id)
                ->latest('attendees.created_at');

        if($request->has('export')){
          return $this->listen_export($request, $builder, 'pdfs.organiser_event_attendees', [
            'organiser' => $organiser,
            'event' => $event
          ], "{$event} Attendees");
        }

        $attendees  = $builder->paginate(20)
          ->appends($request->except('page'));


        $ticket_types  = $event->tickets()->get()->pluck('name','id')->toArray();

        return view('organiser.attendees.index',[
            'organiser' => $organiser,
            'event' => $event,
            'attendees' => $attendees,
            'ticket_types' => $ticket_types
        ])->with('page_title',"Event Orders");
    }

    public function show(Organiser $organiser, Event $event, Request $request)
    {
      $term  =  $request->get('term');
      $attendee = null;

      if($term){
        $attendee = Attendee::query()
              ->search($term)
              ->where('event_id', $event->id)
              ->complete()
              ->first();
      }

      if($attendee)
        $attendee->load(['event','ticket','order','order.user']);
      
      return view('organiser.attendees.view', [
        'attendee' => $attendee,
        'organiser' => $organiser,
        'event' => $event
      ])->with("page_title", "Search Ticket");
    }

    public function checkIn(Organiser $organiser, Event $event,Request $request)
    {
        $this->validate($request, [
           'ticket_no' => "required|exists:attendees,ref_no",
        ]);

        $ticket_no = $request->get('ticket_no');

        $ticket = Attendee::get_by_ticket_no($ticket_no);
        $ticket->load(['event', 'order', 'ticket']);

        if($ticket->check_in_time){
            return redirect()->back()
            ->with('alerts', [
                        ['type' => 'danger', 'message' => "Ticket has already been checked in!"]
                    ]);
        }

        $ticket = $ticket->check_in($request->user());

        return redirect()->back()
            ->with('alerts', [
                        ['type' => 'success', 'message' => "Check in successful!"]
                    ]);
    }
}
