<?php

namespace App\Models;

use App\Traits\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

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
        'pickup_time',
        'goods_type_id',
        'transport_type_id',
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

    public  function goodTypes()
    {
        return $this->belongsTo(GoodsType::class, 'goods_type_id', 'id');
    }
    
}
