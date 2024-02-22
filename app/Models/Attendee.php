<?php

namespace App\Models;

use App\Events\CheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    protected $table = 'attendees';

    protected $fillable = [
        'order_id', 'event_id', 'ticket_id','user_id','ref_no','first_name','last_name','email','phone',
        'status', 'check_in_time','is_refunded', 'pk'
    ];

    public static $ticket_storage_path = 'app/pdfs/tickets';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the full name of the attendee.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @param $ticket_no
     * @return self|null
     */
    public static function get_by_ticket_no($ticket_no)
    {
        return self::query()->where('ref_no', $ticket_no)->first();
    }

    /**
     * Get path of order ticket
     *
     * @return string
     */
    public function get_ticket_path()
    {
        return self::$ticket_storage_path."/{$this->ref_no}.pdf";
    }

    /**
     * Create attendee and return instance if successful
     * @param array $props
     *
     * @return Attendee
     */
    public static function add_attendee(array $props)
    {
        $attendee = new self();
        $attendee->fill(map_props_to_params($props, $attendee->fillable));
        $attendee->save();

        return $attendee;
    }

    public function check_in(User $user)
    {
        $this->user_id = $user->id;
        $this->check_in_time = Carbon::now();
        $this->status = 'checked-in';
        $this->save();

        event(new CheckIn($this, $user));

        return $this;
    }


    /**
     * Generate a private reference number for the attendee. Use for checking in the attendee.
     *
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->ref_no = str_pad(rand(0, pow(10, 9) - 1), 9, '0', STR_PAD_LEFT);
            $item->pk = \Uuid::generate()->string;
        });
    }

    public function generate_ticket($force  = true)
    {
        $attendee = $this;
        $dir_path  = self::get_or_create_tickets_directory();
        $file_name = $attendee->ref_no;
        $file_path = $dir_path. '/' . $file_name;
        $file_with_ext = $file_path . ".pdf";

        if (file_exists($file_with_ext && !$force)) {
            return;
        }

        $order = $this->order;

        $data = [
            'order'     => $order,
            'event'     => $order->event,
            'ticket' => $attendee->ticket,
            'attendee' => $attendee,
        ];

        $pdf = \PDF::loadHtml(view('pdfs.ticket', $data)->render());
        $pdf->save($file_with_ext, $force);
    }

    public static function get_or_create_tickets_directory()
    {
        $dir = storage_path(self::$ticket_storage_path);
        if (!is_dir($dir)) {
            \File::makeDirectory($dir, 0777, true, true);
        }

        return $dir;
    }

    /**
     * [filter description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public static function filter(Request $request)
    {
      $builder  = self::query();

      foreach(get_filters_from_request($request) as $key => $value){
        //if value is not empty
        if (trim($value) != ""){
            switch ($key){
                case 'order_no':
                    $builder->whereHas('order', function($query) use ($value){
                      $query->where('orders.ref_no', $value);
                    });
                    break;
                case 'name':
                    $builder->where('first_name', 'like', "%$value%")
                      ->orWhere('last_name', 'like', "%$value%");
                    break;
                case 'status':
                    $builder->where('attendees.status',$value);
                    break;
                case 'ticket_no':
                    $builder->where("attendees.ref_no", $value);
                    break;
                case 'ticket_type':
                    $builder->whereIn('ticket_id', is_array($value) ? $value : [$value]);
                    break;
                default:
                    break;
            }
        }
      }

      return $builder;
    }

    public function scopeSearch($query, $term) 
    {
        return $query->where('attendees.ref_no', $term)
            ->orWhereHas('order', function($q) use ($term){
                $q->where('orders.ref_no', $term)
                    ->orWhere('orders.phone',encode_phone_number($term))
                    ->orWhere('orders.first_name', 'like', "%{$term}%")
                    ->orWhere('orders.last_name', 'like', "%{$term}%");
            })
            ->orWhere('attendees.first_name', 'like', "%{$term}%")
            ->orWhere('attendees.last_name', 'like', "%{$term}%");
            
    }

    public function scopeComplete($query)
    {
        return $query->WhereHas('order', function($q){
                $q->complete();
            });
    }

    public function get_status_label()
    {
        switch ($this->status) {
            case 'checked-in':
                return '<span class="label label-success"> Checked In </span>';
            case 'pending':
                return '<span class="label label-warning"> not checked in</span>';
            default:
                break;
        }
    }
}
