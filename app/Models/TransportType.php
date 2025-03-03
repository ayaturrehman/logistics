<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
    /** @use HasFactory<\Database\Factories\TransportTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function GoodsType()
    {
        return $this->hasOne(GoodsType::class);
    }
}
