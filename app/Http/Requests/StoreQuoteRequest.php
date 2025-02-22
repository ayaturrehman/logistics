<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'pickup_locations' => 'required|array|min:1',
            'pickup_locations.*.text' => 'required|string',
            'pickup_locations.*.latitude' => 'required|numeric',
            'pickup_locations.*.longitude' => 'required|numeric',
            'stops' => 'nullable|array',
            'stops.*.text' => 'required|string',
            'stops.*.latitude' => 'required|numeric',
            'stops.*.longitude' => 'required|numeric',
            'dropoff_locations' => 'required|array|min:1',
            'dropoff_locations.*.text' => 'required|string',
            'dropoff_locations.*.latitude' => 'required|numeric',
            'dropoff_locations.*.longitude' => 'required|numeric',
            'estimated_distance' => 'required|numeric|min:1',
        ];
    }
}
