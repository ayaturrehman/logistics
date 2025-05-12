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
            ->sum('amount_due');

        $data['last_month_quotes'] = Quote::whereMonth('created_at', date('m', strtotime('-1 month')))
            ->whereYear('created_at', date('Y'))
            ->sum('amount_due');

        $data['recent_quotes'] = Quote::orderBy('created_at', 'desc')
            ->take(10)
            ->get(['id', 'amount_due', 'created_at']);

        return response()->json([
            'data' => $data,
            'message' => 'Dashboard data retrieved successfully',
            'status' => true,
            'code' => 200
        ]);
    }
}
