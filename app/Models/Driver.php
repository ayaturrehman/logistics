<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    /** @use HasFactory<\Database\Factories\DriverFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'type',
        'base_fee',
        'per_mile_rate',
        'commission_rate',
        'fixed_salary',
        'license_number',
        'license_expiry',
        'dvla_report',
        'insurance_policy_number',
        'insurance_expiry',
        'owns_vehicle',
        'years_of_experience',
        'certifications',
        'available',
        'base_fee',
        'per_mile_rate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($driver) {
            if ($driver->user) {
                $driver->user->delete();    
            }
        });
    }
}
