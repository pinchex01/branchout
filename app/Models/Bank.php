<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    protected $fillable = [ 'name','paybill', 'status'];

    public function __toString()
    {
        return $this->name;
    }


    public function bank_accounts()
    {
        return $this->hasMany(BankAccount::class,'bank_id');
    }

    public function scopeName($query, $value)
    {
        if (empty($value)) return $query;

        return $query->where('banks.name',$value);
    }

    public function scopeNameLike($query, $value = null)
    {
        if (empty($value)) return $query;

        return $query->whereRaw("banks.name LIKE '%{$value}%'");
    }

    public function scopeBranchLike($query, $value = null)
    {
        if (empty($value)) return $query;

        return $query->whereRaw("banks.branch LIKE '%{$value}%'");
    }


    public function getStatusLabelAttribute()
    {
        switch ($this->status)
        {
            case $this::STATUS_DISABLED:
                return '<span class="label label-danger">Disabled</span>';
            case $this::STATUS_ACTIVE:
                return '<span class="label label-success">Active</span>';
            default:
                return '<span class="label label-danger">Undefined</span>';
        }
    }
}
