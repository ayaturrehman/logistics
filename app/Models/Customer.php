<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;


    protected $fillable = [
        'business_id',
        'user_id',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'status'
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
