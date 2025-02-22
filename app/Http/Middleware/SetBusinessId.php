<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetBusinessId
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->business_id) {
            Session::put('business_id', auth()->user()->business_id);
        }

        return $next($request);
    }
}
