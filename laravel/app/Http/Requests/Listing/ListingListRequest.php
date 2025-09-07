<?php

namespace App\Http\Requests\Listing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListingListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_uuid' => ['sometimes', 'uuid', 'exists:categories,uuid'],
            'city_uuid' => ['sometimes', 'uuid', 'exists:cities,uuid'],
            'district_uuid' => ['sometimes', 'uuid', 'exists:districts,uuid'],
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
