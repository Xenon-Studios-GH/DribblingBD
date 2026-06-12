<?php

namespace App\Services\Website;

use App\Models\WebsiteProject;
use App\Models\WebsiteProjectImage;
use App\Services\WorkLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    private const ALLOWED_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    private const MAX_SIZE = 5120;
    private const MIN_IMAGES = 1;
    private const MAX_IMAGES = 4;

    private WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

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
        $deletedFiles = [];
        $createdImages = [];

        DB::transaction(function () use ($project, $slots, $removeSlots, $path, &$deletedFiles, &$createdImages) {
            // Remove images specified for deletion
            foreach ($removeSlots as $sortOrder) {
                $existing = $project->images()->where('sort_order', $sortOrder)->lockForUpdate()->first();
                if ($existing) {
                    $deletedFiles[] = $existing->image_path;
                    $existing->delete();
                }
            }

            // Process each slot
            foreach ($slots as $sortOrder => $file) {
                if ($file instanceof UploadedFile) {
                    // Delete existing image for this slot
                    $existing = $project->images()->where('sort_order', $sortOrder)->lockForUpdate()->first();
                    if ($existing) {
                        $deletedFiles[] = $existing->image_path;
                        $existing->delete();
                    }

                    $extension = $this->getSecureExtension($file->getMimeType());
                    $filename = $sortOrder . '_' . time() . '_' . Str::random(4) . '.' . $extension;
                    $filePath = $file->storeAs($path, $filename, 'public');

                    $createdImages[] = WebsiteProjectImage::create([
                        'project_id' => $project->id,
                        'image_path' => $filePath,
                        'sort_order' => $sortOrder,
                    ]);
                }
            }

            // Log the operation
            $this->workLogService->log(
                'Project Images Synced',
                'website',
                $project->id,
                'Synced images for project: ' . ($project->product?->product_name ?? $project->slug)
            );
        });

        // Clean up deleted files after successful transaction
        foreach ($deletedFiles as $filePath) {
            Storage::disk('public')->delete($filePath);
        }
    }

    public function deleteAllImages(WebsiteProject $project): void
    {
        $dir = 'website/projects/' . $project->id;
        Storage::disk('public')->deleteDirectory($dir);
        $project->images()->delete();

        $this->workLogService->log(
            'Project Images Deleted',
            'website',
            $project->id,
            'Deleted all images for project: ' . ($project->product?->product_name ?? $project->slug)
        );
    }

    private function getSecureExtension(string $mime): string
    {
        return self::ALLOWED_EXTENSIONS[$mime] ?? 'bin';
    }
}
