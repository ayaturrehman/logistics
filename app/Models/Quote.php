<?php

namespace App\Models;

use App\Traits\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory, BusinessScope;

    protected $fillable = [
        'business_id',
        'customer_id',
        'vehicle_type_id',
        'pickup_locations',
        'stops',
        'dropoff_locations',
        'estimated_distance',
        'estimated_fare',
        'status',
    ];

    protected $casts = [
        'pickup_locations' => 'array',
        'stops' => 'array',
        'dropoff_locations' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }
}
