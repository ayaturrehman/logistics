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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id'                   => 'required|exists:customers,id',
            'good_type'                     => 'required|exists:goods_types,id',
            'transport_type'                 => 'nullable|exists:transport_types,id',
            'customer_id'                   => 'required|exists:customers,id',
            'vehicle_type'                  => 'required|exists:vehicle_types,id',
            'pickup_location'               => 'required|array|min:1',
            'pickup_location.text'          => 'required|string',
            'pickup_location.latitude'      => 'required|numeric',
            'pickup_location.longitude'     => 'required|numeric',
            'stops'                         => 'nullable|array',
            'stops.*.text'                  => 'required|string',
            'stops.*.latitude'              => 'required|numeric',
            'stops.*.longitude'             => 'required|numeric',
            'dropoff_location'              => 'required|array|min:1',
            'dropoff_location.text'         => 'required|string',
            'dropoff_location.latitude'     => 'required|numeric',
            'dropoff_location.longitude'    => 'required|numeric',
            'estimated_distance'            => 'required|numeric|min:1',
        ];
    }
}
