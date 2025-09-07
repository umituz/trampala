<?php

namespace App\Http\Requests\Listing;

use App\Rules\ImageRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListingCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Listing\Listing::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'max:5000', 'min:10'],
            'category_uuid' => ['required', 'uuid', 'exists:categories,uuid'],
            'country_uuid' => ['required', 'uuid', 'exists:countries,uuid'],
            'city_uuid' => ['required', 'uuid', 'exists:cities,uuid'],
            'district_uuid' => ['nullable', 'uuid', 'exists:districts,uuid'],
            'image' => ['required', new ImageRule(2, ['jpeg', 'png', 'jpg', 'webp'])],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Listing name is required.',
            'name.min' => 'Listing name must be at least 3 characters.',
            'name.max' => 'Listing name cannot exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 10 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'category_uuid.required' => 'Category is required.',
            'category_uuid.exists' => 'Selected category does not exist.',
            'city_uuid.required' => 'City is required.',
            'city_uuid.exists' => 'Selected city does not exist.',
            'district_uuid.uuid' => 'District must be a valid ID.',
            'district_uuid.exists' => 'Selected district does not exist.',
            'image.required' => 'An image is required.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_uuid' => auth()->id(),
        ]);
    }
}
