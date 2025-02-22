<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Order::with(['customer', 'vehicleType', 'driver'])->get());
    }

    /**
     * Show the form for creating a new resource.
     */


    public function createFromQuote(Request $request, $quoteId)
    {
        $quote = Quote::findOrFail($quoteId);

        if ($quote->status !== 'approved') {
            return response()->json(['error' => 'Only approved quotes can be converted into orders'], 400);
        }

        $order = Order::create([
            'business_id'       => $quote->business_id,
            'customer_id'       => $quote->customer_id,
            'vehicle_type_id'   => $quote->vehicle_type_id,
            'pickup_locations'  => $quote->pickup_locations,
            'stops'             => $quote->stops,
            'dropoff_locations' => $quote->dropoff_locations,
            'total_distance'    => $quote->estimated_distance,
            'total_fare'        => $quote->estimated_fare,
            'status'            => 'pending',
        ]);

        return response()->json([
            'message' => 'Order created successfully from quote',
            'order' => $order
        ], 201);
    }


    public function assignDriver(Request $request, $orderId)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id'
        ]);

        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Driver can only be assigned to pending orders'], 400);
        }

        $order->update([
            'driver_id' => $validated['driver_id'],
            'status' => 'assigned'
        ]);

        return response()->json([
            'message' => 'Driver assigned successfully',
            'order' => $order
        ]);
    }

    public function updateStatus(Request $request, $orderId)
    {
        $validated = $request->validate([
            'status' => 'required|in:assigned,in_progress,completed,canceled'
        ]);

        $order = Order::findOrFail($orderId);
        $order->update($validated);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validate();

        $order = Order::create(array_merge($validated, ['status' => $validated['status'] ?? 'pending']));

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
