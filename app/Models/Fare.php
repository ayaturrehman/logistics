<?php

namespace App\Models;

use App\Traits\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fare extends Model
{
    /** @use HasFactory<\Database\Factories\FareFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'vehicle_type_id',
        'base_fare',
        'per_mile_rate',
        'extra_charge'
    ];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}
