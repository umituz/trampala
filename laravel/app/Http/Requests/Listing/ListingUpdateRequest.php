<?php

namespace App\Http\Requests\Listing;

use App\Rules\ImageRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('listing')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', 'min:3'],
            'description' => ['sometimes', 'string', 'max:5000', 'min:10'],
            'category_uuid' => ['sometimes', 'uuid', 'exists:categories,uuid'],
            'city_uuid' => ['sometimes', 'uuid', 'exists:cities,uuid'],
            'district_uuid' => ['sometimes', 'nullable', 'uuid', 'exists:districts,uuid'],
            'image' => ['sometimes', new ImageRule(2, ['jpeg', 'png', 'jpg', 'webp'])],
        ];
    }
}
