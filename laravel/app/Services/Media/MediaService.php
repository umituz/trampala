<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Service for handling media operations across the application.
 *
 * This service centralizes all media-related operations including
 * uploading, updating, clearing, and managing media collections
 * for any model that implements HasMedia interface.
 *
 * @package App\Services\Media
 */
class MediaService
{
    /**
     * Add media file to model's media collection
     *
     * @param HasMedia $model The model to attach media to
     * @param UploadedFile $file The uploaded file
     * @param string $collection The media collection name
     * @return Media
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function addMedia(HasMedia $model, UploadedFile $file, string $collection = 'default')
    {
        return $model->addMedia($file)->toMediaCollection($collection);
    }

    /**
     * Update media for a model (clears existing and adds new)
     *
     * @param HasMedia $model The model to update media for
     * @param UploadedFile $file The new uploaded file
     * @param string $collection The media collection name
     * @return Media
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function updateMedia(HasMedia $model, UploadedFile $file, string $collection = 'default')
    {
        $this->clearMediaCollection($model, $collection);

        return $this->addMedia($model, $file, $collection);
    }

    /**
     * Clear all media from a specific collection
     *
     * @param HasMedia $model The model to clear media from
     * @param string $collection The media collection name
     * @return void
     */
    public function clearMediaCollection(HasMedia $model, string $collection = 'default'): void
    {
        $model->clearMediaCollection($collection);
    }

    /**
     * Clear all media from all collections of a model
     *
     * @param HasMedia $model The model to clear all media from
     * @return void
     */
    public function clearAllMedia(HasMedia $model): void
    {
        $model->media()->delete();
    }

    /**
     * Add multiple media files to a collection
     *
     * @param HasMedia $model The model to attach media to
     * @param array $files Array of UploadedFile instances
     * @param string $collection The media collection name
     * @return array Array of created media objects
     */
    public function addMultipleMedia(HasMedia $model, array $files, string $collection = 'default'): array
    {
        $mediaItems = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $mediaItems[] = $this->addMedia($model, $file, $collection);
            }
        }

        return $mediaItems;
    }

    /**
     * Check if model has media in specific collection
     *
     * @param HasMedia $model The model to check
     * @param string $collection The media collection name
     * @return bool
     */
    public function hasMedia(HasMedia $model, string $collection = 'default'): bool
    {
        return $model->hasMedia($collection);
    }

    /**
     * Get first media URL from collection
     *
     * @param HasMedia $model The model to get media from
     * @param string $collection The media collection name
     * @param string $conversion The conversion name (optional)
     * @return string|null
     */
    public function getFirstMediaUrl(HasMedia $model, string $collection = 'default', string $conversion = ''): ?string
    {
        if (!$this->hasMedia($model, $collection)) {
            return null;
        }

        return $model->getFirstMediaUrl($collection, $conversion);
    }

    /**
     * Get all media URLs from collection
     *
     * @param HasMedia $model The model to get media from
     * @param string $collection The media collection name
     * @param string $conversion The conversion name (optional)
     * @return array
     */
    public function getMediaUrls(HasMedia $model, string $collection = 'default', string $conversion = ''): array
    {
        return $model->getMedia($collection)->map(function ($media) use ($conversion) {
            return $media->getUrl($conversion);
        })->toArray();
    }

    /**
     * Get media count for a collection
     *
     * @param HasMedia $model The model to count media for
     * @param string $collection The media collection name
     * @return int
     */
    public function getMediaCount(HasMedia $model, string $collection = 'default'): int
    {
        return $model->getMedia($collection)->count();
    }

    /**
     * Handle media attachment for any model and collection
     *
     * @param HasMedia $model The model to attach media to
     * @param UploadedFile|null $file The uploaded file
     * @param string $collection The media collection name
     * @param bool $clearExisting Whether to clear existing media before adding new
     * @return Media|null
     */
    public function handleMedia(HasMedia $model, ?UploadedFile $file, string $collection = 'default', bool $clearExisting = false)
    {
        if (!$file) {
            return null;
        }

        if ($clearExisting) {
            return $this->updateMedia($model, $file, $collection);
        }

        return $this->addMedia($model, $file, $collection);
    }
}
