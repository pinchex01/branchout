<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EventApplicationRequest;
use App\Models\Event;
use App\Models\Application;
use App\Models\Organiser;
use App\Models\SalesPerson;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events  =  Event::with(['organiser'])->get();

        \JavaScript::put([
            'Events' => $events
        ]);

        return response()->json($events, 200);
    }

    public function create(EventApplicationRequest $request)
    {

        $event = null;
        $application  = null;
        \DB::transaction(function () use (&$event, &$application, $request){
            $user = $request->user();
            $event = Event::add_event($request->all(),'draft');
            
            $payload = Application::get_event_application_info_from_request($request);
            $application  = Application::create_application($event,'event', $request->input('name'),"Event application", $payload, $user, 'pending');

            //link application to organiser
            $application->organiser_id  = $event->organiser_id;
            $application->save();
        });

        return response()->json($application, 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, Event::$rules);

        $event = null;
        \DB::transaction(function () use (&$event, $request){
            $event = Event::add_event($request->all());
        });

        return response()->json($event, 200);
    }

    public function update(Event $event, Request $request)
    {
        $this->validate($request,[
            'name' => "required",
            'location' => "required",
            'start_date' => "required|date",
            'end_date' => "nullable|date|after:start_date",
            'organiser_id' => "required|exists:organisers,id",
            'user_id' => "required|exists:users,id",
            'avatar' => "sometimes",
            'description' => "required",
            'bank_account_id' => "required|exists:bank_accounts,id"
        ]);

        $event->fill(map_props_to_params($request->all(), $event->getFillable()));
        $event->save();

        return response()->json($event, 200);
    }

    public function toggleEventStatus(Event $event, Request $request)
    {
        $this->validate($request,[
            'status' => "required|in:draft,published"
        ]);

        $event->status = $request->input('status');
        $event->save();

        return response()->json($event, 200);
    }

    public function addSalesPerson(Event $event, Request $request)
    {
        $this->validate($request,[
            'agent_id' => "required|exists:organisers,id",
        ]);

        \DB::beginTransaction();
        $organiser  = Organiser::findOrFail($request->input('agent_id'));
        //check if user is already a member of the sales team
        if($event->sales_people()->whereIn('organiser_id',[$organiser->id])->count()){
            \DB::rollBack();
            return response([
                'status' => 'fail',
                'message' => "{$organiser} is already a member of your sales team"
            ], 422);
        }

        $salesPerson  = SalesPerson::add_sales_person($event, $organiser);

        //todo: send notification to the user
        \DB::commit();

        return response()->json($salesPerson, 200);
    }

    public function orgListEvents(Organiser $organiser, Request $request)
    {
        $events = $organiser->events()->latest('events.created_at')->get();

        return response()->json($events, 200);
    }

    public function orgEventDetails(Organiser $organiser, Event $event, Request $request)
    {
        $event->load(['orders', 'orders.order_items', 'tickets', 'attendees']);

        return response()->json($event, 200);
    }
}
