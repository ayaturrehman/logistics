<?php

namespace App\Models;

use App\Traits\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, BusinessScope;

    protected $fillable = [
        'business_id',
        'customer_id',
        'vehicle_type_id',
        'driver_id',
        'pickup_locations',
        'stops',
        'dropoff_locations',
        'total_distance',
        'total_fare',
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

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
