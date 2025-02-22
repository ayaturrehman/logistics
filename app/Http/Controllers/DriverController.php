<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetBusinessId;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Driver::with('user')->get();
        return response()->json($drivers);
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
    public function store(StoreDriverRequest $request)
    {

        try {

            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make('defaultpassword'),
                'role'     => 'driver',
            ]);

            $driver = Driver::create([
                'user_id'                 => $user->id, // Link to created user
                'business_id'             => getUserBusinessId(), // Automatically set in Request
                'type'                    => $request->type,
                'commission_rate'         => $request->commission_rate,
                'fixed_salary'            => $request->fixed_salary,
                'license_number'          => $request->license_number,
                'license_expiry'          => $request->license_expiry,
                'dvla_report'             => $request->dvla_report,
                'insurance_policy_number' => $request->insurance_policy_number,
                'insurance_expiry'        => $request->insurance_expiry,
                'owns_vehicle'            => 0,
                'years_of_experience'     => $request->years_of_experience,
                'certifications'          => $request->certifications,
                // 'available'               => $request->available,
            ]);

            return response()->json([
                'message' => 'Driver and user created successfully',
                'driver'  => $driver,
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {
            // return response()->json(['error' => 'Failed to create driver.'], 500);
            return response()->json(['error' => $e], 500);
        }

        // Validate salary vs commission
        // if ($validated['type'] === 'self_employed' && !$validated['commission_rate']) {
        //     return response()->json(['error' => 'Commission rate is required for self-employed drivers'], 400);
        // }

        // if ($validated['type'] === 'salary_based' && !$validated['fixed_salary']) {
        //     return response()->json(['error' => 'Fixed salary is required for salary-based drivers'], 400);
        // }


    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $driver = Driver::with('user')->findOrFail($id); // Fetch driver with user

            // Merge driver and user details into one array
            $responseData = array_merge(
                $driver->toArray(),  // Convert driver object to array
                $driver->user ? $driver->user->toArray() : [] // Merge user details if available
            );

            unset($responseData['user']); // Remove nested user key

            return response()->json([
                'message' => 'Driver fetched successfully',
                'driver' => $responseData, // Send merged data
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Driver not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver $driver)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $user = $driver->user; 

        if ($user && ($request->has('name') || $request->has('email'))) {
            $user->update([
                'name' => $request->input('name', $user->name),
                'email' => $request->input('email', $user->email),
            ]);
        }
        
        $driver->update($request->validated());

        return response()->json([
            'message' => 'Driver updated successfully',
            'driver' => $driver
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        $driver->delete();

        return response()->json(['message' => 'Driver deleted successfully']);
    }
}
