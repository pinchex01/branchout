<?php

namespace App\Models;

use App\Events\EventApproved;
use App\Models\Organiser;
use App\Models\Bank;
use App\Events\ApplicationApproved;
use App\Events\ApplicationCreated;
use App\Events\ApplicationPicked;
use App\Events\ApplicationRejected;
use App\Events\ApplicationReviewed;
use App\Events\BankActivated;
use App\UploadsTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Venturecraft\Revisionable\RevisionableTrait;
use App\Events\OrganiserActivated;

class Application extends Model
{
    use RevisionableTrait, UploadsTrait;

    protected $keepRevisionOf = [
        'name', 'type', 'payload', 'status', 'notes', 'type'
    ];

    protected $table = 'applications';

    protected $fillable = [
        'applicable_id', 'applicable_type', 'name', 'type', 'payload',
        'status', 'notes', 'type', 'organiser_id', 'assigned_id'
    ];

    /**
     * @var array
     */
    public static $types = [
        'merchant' => 'Organiser',
        'event' => 'Event',
        'bank_account' => "Bank Account"
    ];

    /**
     * @var array
     */
    public static $statuses = [
        'approved' => 'Approved',
        'corrections' => 'Corrections',
        'pending' => 'Pending',
        'rejected' => 'Rejected',
        'reviewed' => 'Reviewed'
    ];

    public function __toString()
    {
        return "#{$this->application_no} - {$this->name}";
    }

    public function applicable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'application_id');
    }

    public function current_task()
    {
        return $this->hasOne(Task::class, 'application_id')
            ->whereNull("completed_at");
    }

    public function organiser()
    {
        return $this->belongsTo(Organiser::class, 'organiser_id');
    }

    /**
     * Encode payload before saving
     *
     * @param $value
     */
    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = !empty($value) ? json_encode($value) : json_encode([]);
    }

    public function getPayloadAttribute()
    {
        return json_decode($this->attributes['payload'], true);
    }

    public function getRawSchema()
    {
        return $this->payload;
    }

    /**
     * Create a new application from object with parameter
     *
     * @param $applicable object that application can be made for
     * @param $type
     * @param $name
     * @param $notes
     * @param array $payload application info
     * @param User $user the user who created the application
     * @param $status
     * @param array $other_params
     * @return Application|null
     */
    public static function create_application($applicable, $type, $name, $notes, array $payload, User $user,$status, array $other_params = [])
    {
        $application = new self;
        $application->name = $name;
        $application->application_no = generate_application_no();
        $application->applicable_id = $applicable->id;
        $application->applicable_type = get_class($applicable);
        $application->type = $type;
        $application->notes = $notes;
        $application->payload = $payload;
        $application->status = $status;
        $application->user_id = $user->id;
        $application->fill(map_props_to_params($other_params, $application->getFillable()));
        $application->save();

        //fire event application created
        event(new ApplicationCreated($application));

        return $application;
    }

    /**
     * @param Application $application
     * @param User $user
     * @return Application
     */
    public static function pick(Application $application, User $user)
    {
        \DB::transaction(function () use (&$application, $user) {
            $application->fresh();
            if (!$application->picked) {

                $type = in_array($application->type, ["individual", "business"]) ? 'Landlord Panel' : 'Bank Account';

                if ($application->status == 'pending') {
                    Task::create_task($application, "Review", $user, "review");
                } else {
                    Task::create_task($application, "Approval", $user, "approval");
                }

                $application->assigned_id = $user->id;
                $application->save();


                event(new ApplicationPicked($application, $user));
            }
        });

        return $application;
    }

    public static function review(self $application, Task $task, $comment = null)
    {
        \DB::transaction(function () use (&$application, &$task, $comment) {
            $reviewer = $task->user;

            //complete task
            Task::complete_task($task, $comment);

            $application->status = 'reviewed';
            $application->assigned_id = null;
            $application->save();

            event(new ApplicationReviewed($application, $task, $reviewer));

            activity('feed')
                ->log("$reviewer reviewed application: {$application} to {$application->status}")
                ->causedBy($reviewer);
        });

        return $application;
    }

    public static function send_to_corrections(self $application, Task $task, $comment = null)
    {
        \DB::transaction(function () use (&$application, &$task, $comment) {
            $reviewer = $task->user;
            //complete task
            $application->status = 'corrections';
            $application->save();

            event(new ApplicationReviewed($application, $task, $reviewer));

            activity('feed')
                ->log("$reviewer marked application: {$application} for correction")
                ->causedBy($reviewer);
        });

        return $application;
    }

    public static function reject(self $application, Task $task, $comment = null)
    {
        \DB::transaction(function () use (&$application, &$task, $comment) {
            $reviewer = $task->user;
            Task::complete_task($task, $comment);
            $application->status = 'rejected';
            $application->save();

            switch ($application->type) {
                case in_array($application->type, ["organiser","individual-sales-agent", "corporate-sales-agent"]):
                    //$application->reject_organiser();
                    event(new ApplicationRejected($application, $task, $comment));
                    break;
                case 'bank_account':
                    $application->reject_bank();
                    event(new ApplicationRejected($application, $task, $comment));
                    break;
                default:
                    break;
            }

            activity('feed')
                ->log("$reviewer rejected application: {$application}")
                ->causedBy($reviewer);
        });

        return $application;
    }

    public static function approve(self $application, Task $task)
    {
        \DB::transaction(function () use (&$application, &$task) {
            Task::complete_task($task);
            $approver = $task->user;

            $application->status = 'approved';
            $application->assigned_id = null;
            $application->save();

            switch ($application->type) {
                case in_array($application->type, ["organiser","sales-agent"]):
                    $application->approve_organiser('Active');
                    event(new ApplicationApproved($application, $task));
                    break;
                case 'event':
                    $application->approve_event('Active');
                    event(new ApplicationApproved($application, $task));
                    break;
                case 'bank_account':
                    $application->approve_bank('Active');
                    event(new ApplicationApproved($application, $task));
                    break;
                default:
                    break;
            }

            activity('feed')
                ->log("$approver approved application: {$application} ")
                ->causedBy($approver);
        });

        return $application;
    }

    /**
     * @return Merchant
     */
    protected function approve_organiser()
    {
        $organiser = null;
        $application = $this;
        \DB::transaction(function() use (&$application, &$organiser){
            $user = $application->user;
            $organiser_info = $application->payload['organiser'];
            $data = [];
            collect($organiser_info)->each(function($info, $index) use(&$data){
                $data[$index] = $info['value'];
            });
            $organiser = Organiser::add_merchant($application->type, $data , $user);
            //add some extra features here like fire events

            //add user as director
            $director_role  = Role::get_merchant_admin();
            $organiser->add_user($user,$director_role->id);

            //add bank account
            $bank_info = $application->payload['bank_account'];
            $data = [];
            collect($bank_info)->each(function($info, $index) use(&$data){
                $data[$index] = $info['value'];
            });
            $bank = BankAccount::create_from_attributes($data, $organiser->id, BankAccount::TYPE_ORGANISER);

            //update application with the organiser id and type
            $application->applicable_id = $organiser->id;
            $application->applicable_type  =  get_class($organiser);
            $application->save();
        });

        event(new OrganiserActivated($organiser));

        return $organiser;
    }

    protected function reject_organiser()
    {
        
    }

    /**
     * @return BankAccount
     */
    protected function approve_bank()
    {
        $bank_account = $this->applicable;
        //activated merchant default bank
        $bank_account->status = 'active';
        $bank_account->save();
        event(new BankActivated($bank_account));

        return $bank_account;
    }

    /**
     * @return BankAccount
     */
    protected function reject_bank()
    {
        $bank_account = $this->applicable;
        //activated merchant default bank
        $bank_account->status = 'rejected';
        $bank_account->save();

        return $bank_account;
    }

    public function approve_event()
    {
        $event = $this->applicable;
        $event->status  = 'published';
        $event->save();

        event(new EventApproved($event));

        return $event;
    }

    public static function get_organiser_application_info_from_request(Request $request)
    {
        $o = new Organiser();
        $organiser_info = map_props_to_params($request->all()['organiser'], $o->getFillable());
        $info = [];
        foreach($organiser_info as $k => $v){
            switch($k){
                case 'name':
                    $info[$k] = [ 'label' => "Organiser's Name", "value" => $v];
                    break;
                case 'email':
                    $info[$k] = [ 'label' => "Organiser's Email", "value" => $v];
                    break;
                case 'phone':
                    $info[$k] = [ 'label' => "Organiser's Phone Number", "value" => $v];
                    break;
                case 'avatar':
                    $info[$k] = [ 'label' => "Organiser's Logo", "value" => $v, 'upload' => true];
                    break;
                case 'individual':
                    $info[$k] = [ 'label' => "Is Individual? ", "value" => $v];
                    break;
                default:
                    break;
            }
        }

        return ['organiser' => $info];
    }

    public static function get_sales_agent_application_info_from_request(Request $request)
    {
        $o = new Organiser();
        $organiser_info = map_props_to_params($request->all()['organiser'], $o->getFillable());
        $info = [];
        foreach($organiser_info as $k => $v){
            switch($k){
                case 'name':
                    $info[$k] = [ 'label' => "Agent's Name", "value" => $v];
                    break;
                case 'email':
                    $info[$k] = [ 'label' => "Agent's Email", "value" => $v];
                    break;
                case 'phone':
                    $info[$k] = [ 'label' => "Agent's Phone Number", "value" => $v];
                    break;
                case 'avatar':
                    $info[$k] = [ 'label' => "Agent's Logo", "value" => $v, 'upload' => true];
                    break;
                case 'individual':
                    $info[$k] = [ 'label' => "Is Individual? ", "value" => $v];
                    break;
                default:
                    break;
            }
        }

        return ['organiser' => $info];
    }

    public static function get_bank_account_info_from_request(Request $request)
    {
        $b = new BankAccount();
        $request_data  = $request->all()['bank_account'];
        $b_info = map_props_to_params($request_data, $b->getFillable());
        $b_info['type'] = $type = array_get($b_info,'type', $request->input('bank_account.account_type'));
        $b_info['bank_account_leaf'] = $request->input('bank_account.account_type');
        $b_info['realtime'] = array_get($b_info,'realtime', $request->input('bank_account.realtime'));

        $type_string  = $type == 'bank' ? 'Bank Account' : 'MPESA Paybill';
        $info = [];
        foreach($b_info as $k => $v){
            switch($k){
                case 'type':
                    $info[$k] = [ 'label' => "Account Type", "value" => $v];
                    break;
                case 'bank_id':
                    $info[$k] = [ 'label' => "Bank", 'name' => Bank::find($v)->name, "value" =>$v];
                    break;
                case 'name':
                    $info[$k] = [ 'label' => "{$type_string} Name", "value" => $v];
                    break;
                case 'account_no':
                    $info[$k] = [ 'label' => "{$type_string} No", "value" => $v];
                    break;
                case 'bank_account_leaf':
                    $info[$k] = [ 'label' => "Copy of cheque book leaf (or Credit/Debit card)", "value" => $v, 'upload' => true];
                    break;
                case 'realtime':
                    $info[$k] = [ 'label' => "Real-time settlement? ", 'name' => $v ? 'Yes': 'No', "value" => $v];
                    break;
                default:
                    break;
            }
        }

        return ['bank_account' => $info];
    }

    /**
    * Get event application date from request
    *
    * @return array
    */
    public static function get_event_application_info_from_request(Request $request)
    {
        $e = new Event();
        $request_data  = $request->all();
        $e_info = map_props_to_params($request_data, $e->getFillable());
        $e_info['contract'] = $request->input('contract');
        $info = [];
        foreach($e_info as $k => $v){
            switch($k){
                case 'name':
                    $info[$k] = [ 'label' => "Event Name", "value" => $v];
                    break;
                case 'description':
                    $info[$k] = [ 'label' => "Description", "value" =>$v];
                    break;
                case 'bank_account_id':
                    $info[$k] = [ 'label' => "Bank Account", 'name' => BankAccount::find($v)->full_account_name, "value" => $v];
                    break;
                case 'start_date':
                    $info[$k] = [ 'label' => "Event Start Date", "value" => $v];
                    break;
                case 'end_date':
                    $info[$k] = [ 'label' => "Event Start Date", "value" => $v];
                    break;
                case 'avatar':
                    $info[$k] = [ 'label' => "Event Poster", "value" => $v, 'upload' => true];
                    break;
                case 'location':
                    $info[$k] = [ 'label' => "Location/Venue", "value" => $v];
                    break;
                case 'lat':
                    $info[$k] = [ 'label' => "Location (Latitude)", "value" => $v];
                    break;
                case 'lng':
                    $info[$k] = [ 'label' => "Location (Longitude)", "value" => $v];
                    break;
                case 'contract':
                    $info[$k] = [ 'label' => "Artist Contract", "value" => $v, 'upload' => true];
                    break;
                default:
                    break;
            }
        }

        return ['event' => $info];
    }

    public static function filter(Request $request)
    {
        $builder = self::query();

        $filters = $request->get('filters');
        if ($filters) {
            foreach ($filters as $key => $value) {

                //if value is not empty
                if (trim($value) != "") {
                    switch ($key) {
                        case 'status':
                            $builder->ofStatus($value);
                            break;
                        case 'type':
                            $builder->ofType($value);
                            break;
                        default:
                            break;
                    }
                }

            }

        }

        return $builder;
    }

    public function scopeOfStatus($query, $status = null)
    {
        if (empty($status))
            return $query;

        $status = is_array($status) ? $status : [$status];

        return $query->whereIn('status', $status);
    }

    public function scopeOfType($query, $type = null)
    {
        if (empty($status))
            return $query;

        $type = is_array($type) ? $type : [$type];

        return $query->whereIn('status', $type);
    }

    public function scopeMyTasks($query, User $user, $completed = false)
    {
        return $query->whereHas('tasks', function ($query) use ($user, $completed) {
            $query->where('tasks.user_id', $user->id);
            if ($completed) {
                $query->whereNotNull("completed_at");
            } else {
                $query->whereNull("completed_at");
            }
        });
    }

    public function scopeOfQueue($query, User $user)
    {
        $stages = [];
        if ($user->can('review-applications'))
            $stages[] = "pending";

        if ($user->can('approve-applications'))
            $stages[] = "reviewed";

        return $query->whereIn("status", $stages)
            ->whereNull('assigned_id');
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return '<span class="label label-success">Approved</span>';
            case 'corrections':
                return '<span class="label label-info">Corrections</span>';
            case 'pending':
                return '<span class="label label-warning">Initial Review</span>';
            case 'rejected':
                return '<span class="label label-danger">Rejected</span>';
            case 'reviewed':
                return '<span class="label label-primary">Final Approval</span>';
            default:
                return '<span class=""></span>';
        }
    }

    /**
     * @return int
     */
    public function getPickedAttribute()
    {
        return $this->assigned_id ? 1 : 0;
    }
}
