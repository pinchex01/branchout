<?php

namespace App\Models;

use App\AccountableInterface;
use App\AccountableTrait;
use App\HasAvatar;
use App\Jobs\SendSms;
use App\Mail\Welcome;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\Activitylog\Traits\CausesActivity;

class User extends Authenticatable implements AccountableInterface
{
    use LaratrustUserTrait;
    use Notifiable, HasAvatar, AccountableTrait, CausesActivity;

    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';
    const STATUS_PENDING = 'pending';

    protected $table = 'users';

    protected $account_type  = Account::AC_USER;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_number', 'password', 'email','first_name','last_name','other_name','citizenship',
        'dob','gender','avatar','phone', 'pk'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','api_token'
    ];

    public function __toString()
    {
        return $this->full_name;
    }

    /**
     *
     */
    public function organisers()
    {
        return $this->belongsToMany(Organiser::class,"merchant_users","user_id","merchant_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(SalesPerson::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    public function merchant_roles(/*$merchants_ids */)
    {
        return $this->belongsToMany(Role::class,'merchant_users','user_id', 'role_id');
            //->whereIn('merchant_users.merchant_id', is_array($merchants_ids) ? $merchant_ids : [$merchant_ids]);
    }

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['password'] = !!$value?bcrypt($value):null;
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
     * @param $value
     */
    public function setFirstNameAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['first_name'] = !!$value ? strtoupper($value) : null;
    }


    /**
     * @param $value
     */
    public function setLastNameAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['last_name'] = !!$value ? strtoupper($value) : null;
    }


    /**
     * @param $value
     */
    public function setOtherNameAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['other_name'] = !!$value ? strtoupper($value) : null;
    }

    /**
     * @param $value
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['id_number'] = $value;
    }

    /**
     * @param $value
     */
    public function getUsernameAttribute()
    {
        return $this->id_number;
    }

    /**
     * @param $id_number
     *
     * @return User|null
     */
    public static function find_by_id_number($id_number)
    {
        return self::where('id_number','=' ,$id_number)->orWhere('email',$id_number )->orWhere('phone', encode_phone_number($id_number))->first();
    }

    /**
     * Check if user exists else look up the user on IPRS and create the user
     *
     * @param $username
     * @param array|null $attributes
     * @return bool|User
     */
    public static function getOrCreate($username, array $attributes  = null, $force = false)
    {
        $user = null;

        //check if user is registered
        $user  = self::find_by_id_number($username);
        if ($user)
            return $user;

        $data  = iprs_lookup(array_get($attributes, 'username',$username));

        //fail if not in force mode
        if (!$force && !$data)
            return null;

        try {
          //if in force mode create user from params
          $user = new self;
          if(!$data) {
              $user->fill(map_props_to_params($attributes, $user->getFillable()));
          }else{
              //create user

              $data->email = array_get($attributes,'email');
              $data->phone =  array_get($attributes,'phone');
              $user->fill(map_props_to_params($data, $user->getFillable()));
          }
          $user->password = array_get($attributes, 'password', str_random(7));
          $user->status = 'pending';
          $user->id_number = array_get($attributes, 'username', encode_phone_number(array_get($attributes, 'phone')));
          $user->gender = array_get($attributes, 'gender', 'Male');
          $user->save();

          
          event(new \App\Events\UserCreated($user, !array_get($attributes, 'password', null)));

        } catch (\Illuminate\Database\QueryException $e) {
          \Log::info("User creation error: ", [$e->getMessage()]);
          return null;
        }


        return $user;
    }

    /**
     * Boot all of the bootable traits on the model.
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            $user->full_name  =  "{$user->first_name} {$user->other_name} {$user->last_name}";
            $user->pk = \Uuid::generate()->string;
        });

        self::created(function ($user){
            Account::getOrCreate($user);
        });
    }

    /**
     * Generate user api key
     *
     * @return $this
     */
    public function generateApiToken()
    {
        $payload_string = join("",[$this->full_name,$this->dob, $this->gender, $this->id, $this->password ]);

        $hash = hash_payload($payload_string, $this->id_number);

        $this->api_token = $hash;
        $this->save();
    }

    /**
     * Verify user api_key
     *
     * @param $token
     * @param $id_number
     * @return User|false
     */
    public static function verifyApiToken($token, $id_number)
    {
        $user = self::find_by_id_number($id_number);

        if (!$user)
            return false;

        if($token === $user->api_token)
            return $user;

        return false;
    }

    /**
     * Add or deduct points from user
     *
     * @param $points
     * @return User
     */
    public function add_points($points)
    {
        $user = $this;
        \DB::transaction(function () use(&$user, $points){
            $user->points += $points;
            $user->save();

            //todo: fire any events related to user points here
        });

        return $user;
    }

    public function getAccountName()
    {
        return $this->full_name;
    }

    /**
     * @return Role|false
     */
    public function is_admin()
    {
        return $this->get_backend_roles()->first() ? true: false;
    }

    
    public function getIsAdminAttribute()
    {
        return $this->get_backend_roles()->first() ? true: false;
    }

    public function scopeSearch($query, $term)
    {
      return $query->where('full_name', 'like',  "%".strtoupper($term)."%")
          ->orWhere('id_number', 'like', "%$term%")
          ->orWhere('phone', 'like', "%".encode_phone_number($term)."%")
          ->orWhere('email', 'like', "%$term%");
    }

    public static function filter(Request $request)
    {
        $builder  = User::query();

        $filters = $request->get('filters');
        if ($filters){
            foreach ($filters as $key => $value){
                //if value is not empty
                if (trim($value) != ""){
                    switch ($key){
                        case 'id_number':
                            $builder->ofIdNumber(is_array($value) ? $value : [$value]);
                            break;
                        case 'role_id':
                            $builder->ofRole(is_array($value) ? $value : [$value]);
                            break;
                        case 'q':
                            $builder->search($value);
                            break;
                        default:
                            break;
                    }
                }

            }

        }

        return $builder;
    }

    /**
     * Smart cache user roles depending on route and applicable roles
     *
     * @return \Illuminate\Support\Collection
     */
    public function cachedRoles()
    {
        $cacheKey = 'laratrust_roles_for_user_' . $this->getKey();

        return \Cache::remember($cacheKey, \Config::get('cache.ttl', 60), function () {
            if (current_route_is("merchant.*")){
                return $this->get_merchant_roles(merchant());
            }elseif(current_route_is("backend.*")){
                return $this->get_backend_roles();
            }else{
                return $this->roles()->get();
            }
        });
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status)
        {
            case $this::STATUS_ACTIVE:
                return '<span class="label label-success"> Active</span>';
            case $this::STATUS_DISABLED:
                return '<span class="label label-danger"> Disabled</span>';
            case $this::STATUS_PENDING:
                return '<span class="label label-warning"> Pending</span>';
            default:
                return '';
        }
    }

    public function scopeOfRole($query, array $role_ids)
    {
        if (empty($role_ids))
            return $query;

        return $query->whereHas('roles',function($q) use ($role_ids){
            $q->whereIn('roles.id',$role_ids);
        });
    }
    public function scopeOfIdNumber($query, $id_numbers)
    {
        if (empty($id_numbers))
            return $query;

        return $query->whereIn('users.id_number',$id_numbers);
    }

    /**
     * Get merchant specific user role
     */
    public function get_merchant_roles(Organiser $organiser)
    {
        $role_id  = \DB::table("merchant_users")
            ->select("role_id")
            ->where("user_id", $this->id)
            ->where("merchant_id", $organiser->id)
            ->first();

        if(!$role_id)
            return null;

        return Role::where("id", $role_id->role_id)->get();
    }

    /**
     * Get backend role for user
     *
     * @return Role|null
     */
    public function get_backend_roles()
    {
        $role  = $this->roles()->where('type','system')->get();

        return $role;
    }

    /**
     * add roles to user, skip roles already mapped
     *
     * @param  $role_ids
     */
    public function addRoles($role_ids = null)
    {
        $role_ids = is_array($role_ids) ? $role_ids : [$role_ids];

        if (empty($role_ids)) return;

        $skip_ids = array();

        // select role_ids to skip
        $skip_ids = $this->role_ids;

        //remove existing roles from the provided set or role_ids already taken by the user
        $roleIds = array_diff($role_ids,$skip_ids);

        //if no new roles escape
        if(empty($roleIds)) return;

        //add the new roles
        \DB::table('role_user')->insert(
            array_map(function($role_id) {
                return [
                    'role_id' => $role_id,
                    'user_id' => $this->id,
                    'user_type' => get_class($this)
                ];
            }, $roleIds)
        );
    }

    /**
     * Return user role ids
     * @return array
     */
    public function getRoleIdsAttribute()
    {
        if (!$this->roles)
            return [];

        $roles = $this->roles->pluck('id')->toArray();
        return $roles;
    }

    public static function get_login_credentials_from_request(Request $request)
    {
        $identity = self::get_username_type($request->input('username'));
        $username  = $identity == 'phone' ? encode_phone_number($request->input('username')) : $username;

        return $credentials = [
            $identity => $username,
            'password' => $request->get('password'),
        ];
    }

    public static function get_username_type($username)
    {
        $identity = filter_var($username, FILTER_VALIDATE_EMAIL)? 'email' : 'phone';

        return $identity;
    }

    public static function get_by_username($username)
    {
        $identity = self::get_username_type($username);
        if ($identity == 'phone') $username  = encode_phone_number($username);

        return self::where([$identity => $username])->first();
    }

    public function getFullName()
    {
        return $this->full_name ? : $this->fist_name." ".$this->other_name." ".$this->last_name;
    }

    public function send_otp($reason)
    {
        $code  =  rand(10000,99999);
        $str = strtoupper(str_random(4));

        #send sms for complete order
        $message = "Dear Customer, activation code for {$reason} and reference number ending {$str} is {$code}";
        dispatch(new SendSms($this->phone, $message));

        return [$str, $code];
    }

    public function getId()
    {
      return $this->pk;
    }

    public function send_welcome_notification($password  = null)
    {
        $user  =  $this;
        #send sms for complete order
        $message = "Hi, {$user->first_name}. Welcome to PartyPeople. Your account has been created successfully. Signin to partypeople.co.ke using username: {$user->username}";
        if($password){
            $message .=  " and password: {$password}";
        }

        dispatch(new SendSms($user->phone, $message));

        #only send email when user has email address
        if ($user->email && settings('enable_order_email', false)) {
            \Mail::to($user)
                ->send(new Welcome($user, $password));
        } 
    }

    public function reset_password()
    {
        $password  = str_random(7);
        $this->password  =  $password;
        $this->change_password = 1;
        $this->save();

        #send sms for complete order
        $message = "Hi, {$this->first_name}. Your password has been reset. Signin to partypeople.co.ke using username: {$this->username}  and password: {$password}. You will be asked to change your password";

        dispatch(new SendSms($this->phone, $message));

        #only send email when user has email address
        if ($this->email && settings('enable_order_email', false)) {
            \Mail::to($user)
                ->send(new \App\Mail\PasswordReset($user, $password));
        }
    }
}
