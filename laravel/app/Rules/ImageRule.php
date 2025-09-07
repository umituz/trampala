<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImageRule implements ValidationRule
{
    protected int $maxSizeInMB;
    protected array $allowedMimeTypes;

    public function __construct(int $maxSizeInMB = 2, array $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'webp'])
    {
        $this->maxSizeInMB = $maxSizeInMB;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if value is an uploaded file
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check if file is valid
        if (!$value->isValid()) {
            $fail('The :attribute is not a valid file.');
            return;
        }

        // Check if file is an image by MIME type
        $mimeType = $value->getMimeType();
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png', 
            'image/jpg',
            'image/webp'
        ];
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $fail('The :attribute must be a valid image file.');
            return;
        }

        // Check file size (convert MB to bytes)
        $maxSizeInBytes = $this->maxSizeInMB * 1024 * 1024;
        if ($value->getSize() > $maxSizeInBytes) {
            $fail("The :attribute size cannot exceed {$this->maxSizeInMB}MB.");
            return;
        }

        // Check file extension
        $extension = strtolower($value->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedMimeTypes)) {
            $allowedTypesString = implode(', ', $this->allowedMimeTypes);
            $fail("The :attribute must be a file of type: {$allowedTypesString}.");
            return;
        }

    }
}
