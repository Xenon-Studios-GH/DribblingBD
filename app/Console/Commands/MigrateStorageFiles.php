<?php

namespace App\Console\Commands;

use App\Models\PendingImageDeletion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MigrateStorageFiles extends Command
{
    protected $signature = 'app:migrate-storage-files';
    protected $description = 'Migrate files from storage/app/public/ to public/uploads/ for shared hosting compatibility';

    public function handle()
    {
        $source = storage_path('app/public');
        $dest = public_path('uploads');
        $moved = 0;
        $skipped = 0;

        if (!File::isDirectory($source)) {
            $this->warn("Source directory does not exist: {$source}");
            return self::SUCCESS;
        }

        $files = File::allFiles($source);

        foreach ($files as $file) {
            $relative = $file->getRelativePathname();
            $targetPath = $dest . '/' . $relative;

            if (File::exists($targetPath)) {
                if (md5_file($file->getRealPath()) === md5_file($targetPath)) {
                    $skipped++;
                    continue;
                }
                $this->warn("Conflict: {$relative} differs — skipping");
                $skipped++;
                continue;
            }

            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($file->getRealPath(), $targetPath);
            $moved++;
            $this->line("Copied: {$relative}");
        }

        // Update PendingImageDeletion records referencing the old 'public' disk
        if (Schema::hasTable('pending_image_deletions')) {
            $updated = PendingImageDeletion::where('disk', 'public')->update(['disk' => 'uploads']);
            if ($updated) {
                $this->info("Updated {$updated} pending deletion record(s) from 'public' to 'uploads' disk.");
            }
        }

        // Create default OG image placeholder if missing
        $ogDest = $dest . '/images/og-default.jpg';
        if (!File::exists($ogDest)) {
            File::ensureDirectoryExists(dirname($ogDest));
            if (function_exists('imagecreatetruecolor') && function_exists('imagejpeg')) {
                $img = imagecreatetruecolor(1200, 630);
                $bg = imagecolorallocate($img, 30, 30, 30);
                imagefill($img, 0, 0, $bg);
                $textColor = imagecolorallocate($img, 255, 255, 255);
                imagestring($img, 5, 10, 10, config('app.name', 'DriddlingBD'), $textColor);
                imagejpeg($img, $ogDest, 80);
                imagedestroy($img);
                $this->info("Created placeholder: images/og-default.jpg");
            } else {
                $this->warn("GD not available — create public/uploads/images/og-default.jpg manually");
            }
        }

        $this->info("Done. {$moved} files copied, {$skipped} skipped.");
        return self::SUCCESS;
    }
}
