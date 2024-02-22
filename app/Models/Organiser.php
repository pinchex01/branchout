<?php

namespace App\Models;

use App\AccountableInterface;
use App\AccountableTrait;
use App\HasAvatar;
use App\SlugableTrait;
use Illuminate\Database\Eloquent\Model;

class Organiser extends Model implements AccountableInterface
{
    use SlugableTrait, HasAvatar, AccountableTrait;

    protected  $table = 'organisers';

    protected $slug_source = 'name';

    protected $account_type  = Account::AC_ORGANISER;

    protected $fillable = [
        'name', 'slug', 'user_id','email','phone','facebook','twitter','about',
        'avatar', 'is_individual', 'code'
    ];

    public static $rules  = [
        'name' => "required|unique:organisers,name",
        'email' => "required|email",
        'phone' => "required|full_phone",
        'type' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'organiser_id');
    }

    public function agent_events()
    {
        return $this->belongsToMany(Event::class,'sales_people','organiser_id','event_id');
    }

    public function sales()
    {
        return $this->hasMany(SalesPerson::class, 'organiser_id');
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class,Event::class,'organiser_id','event_id');
    }

    public function sales_orders()
    {
        return $this->hasManyThrough(Order::class, SalesPerson::class, 'organiser_id', 'sales_person_id', 'id');
    }

    public function banks()
    {
        return $this->hasMany(BankAccount::class, 'owner_id')
            ->where('owner_type', BankAccount::TYPE_ORGANISER);
    }

    public function bank_accounts()
    {
        return $this->hasMany(BankAccount::class, 'owner_id')
            ->where('owner_type', BankAccount::TYPE_ORGANISER);
    }

    /**
     * Get user for merchant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, "merchant_users","merchant_id","user_id");
    }

    public function default_bank()
    {
        return $this->hasOne(BankAccount::class, 'owner_id')
            ->where('owner_type', BankAccount::TYPE_ORGANISER)
            ->where('is_default', 1);
    }

    /**
     * Encode phone number before saving
     * @param $value
     */
    public function setPhoneAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['phone'] = !!$value ? encode_phone_number($value) : null;
    }

    /**
     * @param $type
     * @param array $data
     * @param User $user
     * @return Organiser
     */
    public static function add_merchant($type, array  $data, User $user)
    {
        $organiser = new self;
        $props = map_props_to_params($data, $organiser->fillable);
        $organiser->fill($props);
        $organiser->type = $type;
        $organiser->user_id = $user->id;
        $organiser->is_individual  = array_get($data, 'individual')== 'yes' ? true : false;
        if ($type == 'sales-agent'){
            $organiser->code = rand(100000,999999);
        }
        $organiser->save();

        return $organiser;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($organiser){
            $organiser->slug? : $organiser->generateSlug();
        });

        static::created(function ($organiser){
            Account::getOrCreate($organiser);
        });
    }

    public function getAccountName()
    {
        return $this->name;
    }

    /**
     * Add user to merchant or sync with new roles
     *
     * @param User $user
     * @param $role_ids
     * @return bool
     */
    public function add_user(User $user, $role_ids)
    {
        $role_ids = is_array($role_ids) ? $role_ids : [$role_ids];

        //delete all previous roles assigned
        \DB::table('merchant_users')->where([
            "merchant_id" => $this->id,
            "user_id" => $user->id
        ])->delete();


        $inserts = [];
        foreach ($role_ids as $role_id){
            $inserts[]  = [
                "merchant_id" => $this->id,
                "user_id" => $user->id,
                "role_id" => $role_id
            ];
        }

        return \DB::table('merchant_users')->insert($inserts);
    }

    public function __toString()
    {
        return $this->name;
    }

    public  function scopeAgents($query)
    {
        return $query->where('type','sales-agent');
    }

    public function scopeNameLike($query, $value = null)
    {
        if (empty($value)) return $query;

        return $query->whereRaw("organisers.name LIKE '%{$value}%'");
    }

    public function is_agent($event_id)
    {
        $agent = $this->sales()->where('event_id', $event_id)->first();
        if (!$agent) return false;
        return $agent->code;
    }
}
