<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Venturecraft\Revisionable\RevisionableTrait;

class Tariff extends Model
{
    use RevisionableTrait, LogsActivity;

    protected $table = 'tariffs';

    protected $fillable = [
        'name', 't_floor', 't_ceiling','amount','status'
    ];

    protected static $logAttributes = [
        'name', 't_floor', 't_ceiling','amount','status'
    ];

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @param $floor
     * @param $ceiling
     * @param $amount
     * @param $status
     * @return Tariff
     */
    public static function add_tariff($name, $floor, $ceiling, $amount, $status)
    {
        $tariff = new self([
            'name' => $name,
            't_floor' => $floor,
            't_ceiling' => $ceiling,
            'amount' => $amount,
            'status' => $status
        ]);

        $tariff->save();

        //todo: log actions
        return $tariff;
    }

    public function getAmountPercentageAttribute()
    {
        return $this->amount/100;
    }

    /**
     * @param $amount
     * @return Tariff|null
     */
    public static function get_tariff($amount)
    {
        $tariff = Tariff::whereRaw("t_floor <= ? and t_ceiling >= ?", [$amount, $amount])->first();

        return $tariff;
    }

    /**
     * Get commission based on tariff bracket if bracket is not found return default
     *
     * @param $amount
     * @param int $default
     * @return double
     */
    public static function get_commission($amount, $default = 0)
    {
        $tariff  =  self::get_tariff($amount);
        if ($tariff)
            return $tariff->amount_percentage * $amount;

        return $default;
    }

    /**
     * @param $amount
     * @param $tickets_sold
     * @param SalesPerson $salesPerson
     * @return float
     */
    public static function get_sales_agent_commission($amount, $tickets_sold, SalesPerson $salesPerson)
    {
        $commission = Tariff::get_commission($tickets_sold + $salesPerson->tickets_sold);
        return ($commission * $amount);
    }
}
