<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;

class DashboardController extends Controller
{
    public function index()
    {
        $data['this_month_quotes'] = Quote::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('estimated_fare');

        $data['last_month_quotes'] = Quote::whereMonth('created_at', date('m', strtotime('-1 month')))
            ->whereYear('created_at', date('Y'))
            ->sum('estimated_fare');

        $data['this_month_total_quotes'] = Quote::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();
        $data['last_month_total_quotes'] = Quote::whereMonth('created_at', date('m', strtotime('-1 month')))
            ->whereYear('created_at', date('Y'))
            ->count();

        $data['pending_quotes'] = Quote::where('payment_status', 'pending')
            ->count();

        $data['recent_quotes'] = Quote::with([
            'customer' => fn($q) => $q->select('id', 'user_id'),
            'customer.user:id,name',
            'vehicleType:id,name',
        ])->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            // this new number of customers
        $data['this_month_new_customers'] = Quote::where('created_at', '>=', now()->subMonth())
            ->distinct('customer_id')
            ->count('customer_id');

        $data['last_month_new_customers'] = Quote::where('created_at', '>=', now()->subMonth(2)->startOfMonth())
            ->where('created_at', '<=', now()->subMonth(1)->endOfMonth())
            ->distinct('customer_id')
            ->count('customer_id');

        return response()->json([
            'data' => $data,
            'message' => 'Dashboard data retrieved successfully',
            'status' => true,
            'code' => 200
        ]);
    }
}
