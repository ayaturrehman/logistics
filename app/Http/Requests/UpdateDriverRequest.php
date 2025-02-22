<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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


        $driverId = request()->route()->parameter('driver');

        return [
            'type'                      => 'required|in:self_employed,salary_based',
            'commission_rate'           => 'nullable|numeric|min:0|max:100',
            'fixed_salary'              => 'nullable|numeric|min:0|required_if:type,salary_based',
            'license_number' => [
                'required',
                'string',
                Rule::unique('drivers', 'license_number')
                    ->where('business_id', getUserBusinessId())
                    ->ignore($driverId, 'id'),
            ],
            'license_expiry'            => 'required|date',
            'dvla_report'               => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'insurance_policy_number'   => 'nullable|string',
            'insurance_expiry'          => 'nullable|date',
            'owns_vehicle'              => 'boolean',
            'years_of_experience'       => 'nullable|integer|min:0|max:50',
            'certifications'            => 'nullable|string',
            'available'                 => 'boolean',
        ];
    }
}
