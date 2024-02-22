<?php

namespace App\Models;

use App\AccountableInterface;
use App\AccountableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankAccount extends Model implements AccountableInterface
{
    use AccountableTrait;


    const TYPE_USER = 'User';
    const TYPE_ORGANISER = 'Organiser';
    const TYPE_SYSTEM = 'System';

    protected $table = 'bank_accounts';

    protected $fillable = [
        'name', 'account_no', 'bank_id', 'owner_id', 'type', 'owner_type', 'bank_account_leaf'
    ];

    protected $account_type = Account::AC_BANK;
    public $_totalCredit;
    public $_totalDebit;

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function owner()
    {
        switch ($this->owner_type) {
            case $this::TYPE_ORGANISER;
                return $this->belongsTo(Organiser::class, 'owner_id');
            case  $this::TYPE_USER;
                return $this->belongsTo(User::class, 'owner_id');
        }
    }


    public function __toString()
    {
        return $this->full_account_name;
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class, 'account_id')
            ->where('account_type','Bank');
    }

    public function getMaskedAccountNoAttribute()
    {
        return maskString($this->account_no, 1, strlen($this->account_no) - 4);
    }

    public function scopeJoinBanksTable($query)
    {
        return $query->join('banks', 'banks.id', '=', 'bank_accounts.bank_id');
    }

    public function scopeOfOwner($query, AccountableTrait $account)
    {
        return $query->where('bank_accounts.owner_id', $account->getAccountId())
            ->where('bank_accounts.type', $account->getAccountType());
    }

    public function getFullAccountNameAttribute()
    {
        return  ($this->bank ? $this->bank->name : 'Mpesa Paybill'). " - " . $this->account_no . " - " . $this->name . " ";
    }

    public function getFullNameAttribute()
    {
        return ($this->bank ? $this->bank->name : 'Mpesa Paybill') . " - " . $this->account_no . " - " . $this->name . " ";
    }

    public function getAccountName()
    {
        return $this->full_account_name;
    }

    public static function scopeSystemBankAccounts($query)
    {
        return $query->where('bank_accounts.owner_id', 0)
            ->where('bank_accounts.type', self::TYPE_SYSTEM);
    }

    /**
     * Create bank account from array of attributes
     * Ensure you pass request after validation
     *
     * @param array $attributes
     * @param $owner_id
     * @param $owner_type
     * @return BankAccount|null
     *
     */
    public static function create_from_attributes(array $attributes, $owner_id, $owner_type)
    {
        $bank = null;
        \DB::transaction(function () use (&$bank, $attributes, $owner_id, $owner_type) {

            //check if owner has any default bank set
            $default = self::where([
                "owner_id" => $owner_id,
                "owner_type" => $owner_type,
                'is_default' => 1
            ])->count();

            $bank = new self();
            $bank->fill(map_props_to_params($attributes, $bank->fillable));
            $bank->owner_id = $owner_id;
            $bank->owner_type = $owner_type;
            $bank->is_default = $default ? 0 : 1;
            $bank->save();
        });

        return $bank;
    }

    public function setTypeAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['type'] = !!$value ? $value : 'bank';
    }

    public function setBankIdAttribute($value)
    {
        // only mutate if $value is not null
        $this->attributes['bank_id'] = !empty($value) ? $value : null;
    }

    public function scopeOfAccountNo($query, $account_no)
    {
        return $query->where('bank_accounts.account_no', '=', $account_no);
    }

    public function scopeNameLike($query, $value = null)
    {
        if (empty($value)) return $query;

        return $query->whereRaw("bank_accounts.name LIKE '%{$value}%'");
    }

    public function scopeOfBank($query, array $bank_ids)
    {
        if (empty($bank_ids)) return $query;

        return $query->whereIn('bank_accounts.bank_id', $bank_ids);
    }

    public function scopeFilter($query, Request $request)
    {
        $builder = $query;
        $filters = $request->get('filters');
        if ($filters) {
            foreach ($filters as $key => $value) {

                //if value is not empty
                if (trim($value) != "") {
                    switch ($key) {
                        case 'name':
                            $builder->nameLike($value);
                            break;
                        case 'account_no':
                            $builder->ofAccountNo($value);
                            break;
                        case 'bank_id':
                            $builder->ofBank(is_array($value) ? $value : [$value]);
                            break;
                        default:
                            break;
                    }
                }

            }

        }

        return $builder;
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="label label-success">Active</span>';
            case 'pending':
                return '<span class="label label-warning">Pending</span>';
            case 'rejected':
                return '<span class="label label-disabled">Rejected</span>';
            default:
                return '<span class=""></span>';
        }
    }

    /**
     * @param Organiser $organiser
     * @return BankAccount|null
     */
    public static function get_default_for_organiser(Organiser $organiser)
    {
        return BankAccount::where([
            'owner_id' => $organiser->id,
            'owner_type' => self::TYPE_ORGANISER,
            'is_default' => 1
        ])->first();
    }

    /**
     * @param $owner_type
     * @param $owner_id
     * @return array
     */
    public static function get_validation_rules($owner_id,$owner_type)
    {
        return [
            'owner_id' => "required|integer",
            'owner_type' => "required",
            'account_type' => "required|in:bank,paybill",
            'name' => 'required',
            'account_no' => [
                'required', 'confirmed', 'numeric',
                Rule::unique('bank_accounts', 'account_no')->where('owner_id', $owner_id)
                    ->where('type', $owner_type)
            ],
            'bank_id' => 'required_if:account_type,bank',
            'realtime' => "required|boolean"
        ];
    }

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($item){
            $account = Account::getOrCreate($item);
        });
    }

}
