<?php

use App\Models\Business;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getUserBusinessId')) {
    /**
     * Get the business_id of the authenticated user globally.
     *
     * @return int|null
     */
    function getUserBusinessId()
    {
        if (!Auth::check()) {
            return 1; 
        }

        $business = Business::where('owner_id', Auth::id())->first();

        return $business ? $business->id : null;
    }
}
