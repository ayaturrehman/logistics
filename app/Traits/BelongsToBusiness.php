<?php 
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait BusinessScope
{
    protected static function bootBusinessScope()
    {
        if (auth()->check()) {
            static::addGlobalScope('business', function (Builder $builder) {
                $businessId = Session::get('business_id');

                if ($businessId) {
                    $builder->where('business_id', $businessId);
                } else {
                    $builder->whereRaw('1 = 0');
                }
            });
        }
    }
}
