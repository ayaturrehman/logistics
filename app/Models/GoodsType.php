<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    /** @use HasFactory<\Database\Factories\GoodsTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function tranportTypes()
    {
        return $this->hasOne(TransportType::class, 'goods_type_id', 'id');
    }
}
