<?php

namespace App\Services\Website;

use App\Models\WebsiteProject;
use App\Models\WebsiteProjectImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImageService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    private const MAX_SIZE = 5120;
    private const MIN_IMAGES = 1;
    private const MAX_IMAGES = 4;

    public function validateImages(array $images): void
    {
        $filled = 0;
        foreach ($images as $slot => $file) {
            if ($file instanceof UploadedFile) {
                $filled++;
                $mime = $file->getMimeType();
                if (!in_array($mime, self::ALLOWED_MIMES, true)) {
                    throw ValidationException::withMessages([
                        'images.' . $slot => 'Slot ' . $slot . ': Only JPEG, PNG and WebP images are allowed.',
                    ]);
                }
                if ($file->getSize() > self::MAX_SIZE * 1024) {
                    throw ValidationException::withMessages([
                        'images.' . $slot => 'Slot ' . $slot . ': Image must be under 5MB.',
                    ]);
                }
            }
        }
        if ($filled < self::MIN_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'At least 1 image is required.',
            ]);
        }
        if ($filled > self::MAX_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'Maximum 4 images allowed.',
            ]);
        }
    }

    public function syncImages(WebsiteProject $project, array $slots, array $removeSlots = []): void
    {
        $path = 'website/projects/' . $project->id;

        foreach ($removeSlots as $sortOrder) {
            $existing = $project->images()->where('sort_order', $sortOrder)->first();
            if ($existing) {
                Storage::disk('public')->delete($existing->image_path);
                $existing->delete();
            }
        }

        foreach ($slots as $sortOrder => $file) {
            if ($file instanceof UploadedFile) {
                $existing = $project->images()->where('sort_order', $sortOrder)->first();
                if ($existing) {
                    Storage::disk('public')->delete($existing->image_path);
                    $existing->delete();
                }

                $filename = $sortOrder . '_' . time() . '.' . $file->extension();
                $filePath = $file->storeAs($path, $filename, 'public');

                WebsiteProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $filePath,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }

    public function deleteAllImages(WebsiteProject $project): void
    {
        $dir = 'website/projects/' . $project->id;
        Storage::disk('public')->deleteDirectory($dir);
        $project->images()->delete();
    }
}
