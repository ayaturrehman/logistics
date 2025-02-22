<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description'
    ];

    public function fare()
    {
        return $this->hasOne(Fare::class);
    }
}
