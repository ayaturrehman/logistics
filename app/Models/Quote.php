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
        'goods_type_id',
        'transport_type_id',
        'pickup_time',
        'pickup_locations',
        'stops',
        'dropoff_locations',
        'estimated_distance',
        'estimated_fare',
        'status',
        'collection_place_id',
        'collection_contact_name',
        'collection_contact_phone',
        'collection_contact_email',
        'delivery_place_id',
        'delivery_contact_name',
        'delivery_contact_phone',
        'delivery_contact_email',
        'vehicle_available_from',
        'vehicle_available_to',
        'vehicle_make',
        'vehicle_model',
        'number_plate',
        'gearbox',
        'seating_capacity',
        'comments',
        'payment_method',
        'payment_details',
        'amount_paid',
        'amount_due',
        'payment_status',
    ];

    protected $casts = [
        'pickup_locations' => 'array',
        'stops' => 'array',
        'dropoff_locations' => 'array',
        'payment_details' => 'array',
        'vehicle_available_from' => 'datetime',
        'vehicle_available_to' => 'datetime',
    ];

    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function goodTypes()
    {
        return $this->belongsTo(GoodsType::class, 'goods_type_id', 'id');
    }
}