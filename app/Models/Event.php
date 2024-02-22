<?php

namespace App\Models;

use App\AccountableInterface;
use App\AccountableTrait;
use App\HasAvatar;
use App\SlugableTrait;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model implements AccountableInterface
{
    use HasAvatar, SlugableTrait, AccountableTrait;

    protected $table = 'events';

    protected $slug_source = 'name';

    protected $account_type  = Account::AC_EVENT;


    protected $fillable = [
        'name', 'venue', 'location', 'slug', 'lat', 'lng', 'start_date', 'end_date', 'on_sale_date',
        'description', 'organiser_id', 'user_id', 'status', 'avatar', 'commission', 'bank_account_id',
        'sales_volume','tickets_sold'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static $rules = [
        'name' => "required",
        'location' => "required",
        'start_date' => "required|date|after:yesterday",
        'end_date' => "required|date|after:start_date",
        'on_sale_date' => "sometimes",
        'organiser_id' => "required|exists:organisers,id",
        'user_id' => "required|exists:users,id",
        'avatar' => "required",
        'description' => "required",
        'bank_account_id' => "required|exists:bank_accounts,id"
    ];

    public function __toString()
    {
        return $this->name;
    }

    public function organiser()
    {
        return $this->belongsTo(Organiser::class, 'organiser_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'event_id');
    }

    public function order_items()
    {
        return $this->hasManyThrough(OrderItem::class, Order::class , 'event_id', 'order_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales_people()
    {
        return $this->hasMany(SalesPerson::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class,'bank_account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'item','item_type','item_id');
    }

    /**
    *
    */
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = Carbon::parse($value)->format('Y-m-d H:m:s');
    }

    /**
     * @param $value
     */
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = is_empty($value) ? $this->start_date : Carbon::parse($value)->format('Y-m-d H:m:s');
    }

    public static function add_event($props, $status = 'draft')
    {
        $event = new self;
        $data = map_props_to_params(array_except($props, ['start_date','end_date']), $event->fillable);
        $event->fill($data);
        $event->start_date = Carbon::parse($props['start_date']);
        $event->end_date  = Carbon::parse(array_get($props, 'end_date',$props['start_date']));
        $event->status  = $status;
        $event->save();

        return $event;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            $event->slug ?: $event->generateSlug();
            $event->status = $event->status ?: 'draft';
        });

        static::created(function ($item){
            $account = Account::getOrCreate($item);
        });
    }

    /**
     * @param $session_id
     * @param Event $event
     * @param $tickets
     * @param $expires_at
     * @param User $user
     * @return array
     */
    public static function reserve_tickets($session_id, self $event, $tickets, $expires_at, User $user = null)
    {
        $reservations = [];
        Ticket::whereIn('id', array_keys($tickets))
            ->each(function ($ticket) use ($session_id, $event, $user, $tickets, &$reservations, $expires_at) {
                $reservation = $ticket->reserve($session_id,  $tickets[$ticket->id], $expires_at, $user);
                $reservations[$reservation->id] = [
                    'event_id' => $event->id,
                    'name' => $reservation->ticket->name,
                    'ticket_id' => $reservation->ticket_id,
                    'quantity' => $reservation->quantity,
                    'unit_price' => $reservation->ticket->price,
                    'total' => $reservation->ticket->price * $reservation->quantity,
                    'groups_of' => $ticket->is_group_ticket ? $ticket->min_per_person : 1
                ];
            });

        return $reservations;
    }

    /**
     * -------------------------
     * Ticket design static info
     *-----------------------------
     * */

    public function getTicketBorderColorAttribute()
    {
        return '#000000';
    }

    public function getTicketBgColorAttribute()
    {
        return '#FFFFFF';
    }

    public function getTicketTextColorAttribute()
    {
        return '#000000';
    }

    public function getTicketBarcodeTypeAttribute()
    {
        return 'QRCODE';
    }

    public function getTicketSubTextColorAttribute()
    {
        return '#999999';
    }

    public function getAccountName()
    {
        return $this->__toString();
    }

    public function scopeStatus($query, $status  =  null)
    {
        if(!$status)
            return $query;

        return $query->where('status',$status);
    }

    public function scopeIsPrivate($query, $private = 0)
    {
        if(!$private)
            return $query;

        return $query->where('private',$private);
    }

    public function scopeNameLike($query, $value = null)
    {
        if (empty($value)) return $query;

        return $query->whereRaw("events.name LIKE '%{$value}%'");
    }

    public function scopeLive($query)
    {
        return $query->whereRaw("DATE(NOW()) >= DATE(events.start_date) AND DATE(NOW()) <= events.end_date");
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status)
        {
            case 'published':
                return '<span class="label label-success">Published</span>';
            case 'draft':
                return '<span class="label label-warning">Draft</span>';
            default:
                return '';
        }
    }

    /**
     * Check if ticket is on sale from dates
     */
    public function getOnSaleAttribute()
    {
        $current = Carbon::now()->format('Y-m-d');
        $start_sale  = $this->start_date->format("Y-m-d");
        $end_sale  = $this->end_date->format("Y-m-d");

        return ($start_sale < $current && $end_sale > $current);
    }


    /**
     * Get the destination bank account or return the default for the organiser
     *
     * @return BankAccount
     */
    public function get_destination_bank_account()
    {
        $bank_account = $this->bank_account ? : BankAccount::get_default_for_organiser($this->organiser);

        return $bank_account;
    }


    /**
     * Update event sales stats cache
     * @param  Order   $order   [description]
     * @param  boolean $reverse [description]
     * @return self           [description]
     */
    public function post_order_sales(Order $order, $reverse = false)
    {
        $amount  = $order->amount;
        $tickets  = $order->order_items->sum('quantity');

        $this->fresh();
        #if reverse is true, the substract the order sales info
        $this->sales_volume +=  $reverse ? -$amount  : $amount;
        $this->tickets_sold +=  $reverse ? -$tickets : $tickets;
        return $this->save();
    }
}
