<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Customer::with('user')->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {


        try {

            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make('defaultpassword'),
                'role'     => 'driver',
            ]);

            $customer = Customer::create([
                'user_id'          => $user->id, // Link to created user
                'business_id'      => getUserBusinessId(), // Automatically set in Request
                'phone'            => $request->phone,
                'address'          => $request->address,
                'city'             => $request->city,
                'state'            => $request->state,
                'postal_code'      => $request->postal_code,
                'country'          => $request->country,
                'status'           => $request->status,
            ]);

            return response()->json([
                'message' => 'Customer created successfully',
                'driver'  => $customer,
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $customer = Customer::with('user')->findOrFail($id); // Fetch customer with user

            $responseData = array_merge(
                $customer->toArray(),
                $customer->user ? $customer->user->toArray() : [] // Merge user details if available
            );

            unset($responseData['user']); // Remove nested user key

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'customer not found'], 404);
        }

        return response()->json(Customer::with('user')->findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user; // Get the linked user

        // Update customer details
        $customer->update($request->only([
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'status'
        ]));

        // Update user details (name & email)
        if ($request->has('name') || $request->has('email')) {
            $user->update($request->only(['name', 'email']));
        }

        return response()->json([
            'message' => 'Customer updated successfully',
            'customer' => $customer->load('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
