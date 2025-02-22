<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Get all businesses (Admin only).
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(Business::all());
    }

    /**
     * Get details of a business (Only Owner can access).
     */
    public function show($id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($business);
    }

    /**
     * Create a new business (Only Admins can create).
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:businesses,name',
            'owner_id' => 'required|exists:users,id',
            'email' => 'required|email|unique:businesses,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $business = Business::create($validated);

        return response()->json([
            'message' => 'Business created successfully',
            'business' => $business
        ], 201);
    }

    /**
     * Update business details (Only Owner can update).
     */
    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:businesses,name,' . $id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $business->update($validated);

        return response()->json([
            'message' => 'Business updated successfully',
            'business' => $business
        ]);
    }

    /**
     * Delete a business (Only Owner can delete).
     */
    public function destroy($id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $business->delete();
        return response()->json(['message' => 'Business deleted successfully']);
    }
}
