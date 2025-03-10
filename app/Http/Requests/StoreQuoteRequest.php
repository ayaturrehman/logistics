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
   
    public function rules() : array
    {
        return [
            // Required fields
            'customer_id'           => 'required|integer|exists:customers,id',
            'vehicle_type_id'       => 'required|integer|exists:vehicle_types,id',
            'transport_type_id'     => 'nullable|integer|exists:transport_types,id',
            'estimated_distance'    => 'required|numeric|min:0',
            'estimated_fare'        => 'nullable|numeric|min:0',
            'pickup_time'           => 'nullable|date',
            
            // JSON fields
            'stops'                 => 'nullable|array',
            'stops.*.text'          => 'required|string',
            'stops.*.latitude'      => 'required|numeric',
            'stops.*.longitude'     => 'required|numeric',
            
            // Collection details
            'collection_place_type'     => 'required|string|in:garage,dealership,house,auto,company,branch,shop,other',
            'collection_contact_name'   => 'nullable|string|max:255',
            'collection_contact_phone'  => [
                'nullable',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/'
            ],
            'collection_contact_email'          => 'nullable|email|max:255',
            'pickup_locations'                  => 'required|array',
            'pickup_locations.*.text'           => 'required|string',
            'pickup_locations.*.latitude'       => 'required|numeric',
            'pickup_locations.*.longitude'      => 'required|numeric',
            
            // Delivery details

            
            'delivery_place_type' => 'required|string|in:garage,dealership,house,auto,company,branch,shop,other',
            'delivery_contact_name' => 'nullable|string|max:255',
            'delivery_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/'
            ],
            'delivery_contact_email'            => 'nullable|email|max:255',
            'dropoff_locations'                 => 'required|array',
            'dropoff_locations.*.text'          => 'required|string',
            'dropoff_locations.*.latitude'      => 'required|numeric',
            'dropoff_locations.*.longitude'     => 'required|numeric',
            
            // Vehicle availability
            'vehicle_available_from'            => 'nullable|date|after_or_equal:today',
            'vehicle_available_to'              => 'nullable|date|after_or_equal:vehicle_available_from',
            
            // Vehicle details
            'vehicle_make' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'number_plate' => 'nullable|string|max:255',
            'gearbox' => 'nullable|string|max:255',
            'seating_capacity' => 'nullable|integer|min:1',
            'comments' => 'nullable|string',
            
            // Payment details
            'payment_method' => 'nullable|string|max:255',
            'payment_details' => 'nullable|json',
            'amount_paid' => 'nullable|numeric|min:0',
            'amount_due' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,paid,partially_paid,failed',
            
            // Quote status
            'status' => 'nullable|in:pending,approved,rejected',
        ];
    }

    public function messages()
    {
        return [
            // Required fields
            'vehicle_type_id.required' => 'Please select a vehicle type',
            'estimated_distance.required' => 'Estimated distance is required',
            'estimated_distance.min' => 'Estimated distance must be greater than 0',
            
            // Collection details
            'pickup_locations.required' => 'Pickup locations are required',
            'pickup_locations.array' => 'Pickup locations must be an array',
            'pickup_locations.*.text.required' => 'Pickup location text is required',
            'pickup_locations.*.latitude.required' => 'Pickup location latitude is required',
            'pickup_locations.*.longitude.required' => 'Pickup location longitude is required',
            
            // Delivery details
            'dropoff_locations.required' => 'Dropoff locations are required',
            'dropoff_locations.array' => 'Dropoff locations must be an array',
            'dropoff_locations.*.text.required' => 'Dropoff location text is required',
            'dropoff_locations.*.latitude.required' => 'Dropoff location latitude is required',
            'dropoff_locations.*.longitude.required' => 'Dropoff location longitude is required',
            
            // Vehicle details
            'seating_capacity.min' => 'Seating capacity must be at least 1',
            
            // Contact details
            'collection_contact_phone.regex' => 'Invalid phone number format',
            'delivery_contact_phone.regex' => 'Invalid phone number format',
            'collection_contact_email.email' => 'Invalid email address format',
            'delivery_contact_email.email' => 'Invalid email address format',
            
            // Payment details
            'amount_paid.min' => 'Amount paid cannot be negative',
            'amount_due.min' => 'Amount due cannot be negative',
            'payment_status.in' => 'Invalid payment status',
            
            // Date validations
            'vehicle_available_to.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}
