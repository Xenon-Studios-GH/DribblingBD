<?php

namespace Tests\Feature;

use App\Models\PdfDownload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class CleanOldPdfDownloadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_downloads_older_than_one_year()
    {
        $user = \App\Models\User::factory()->create();

        // Create a download 13 months old (should be deleted)
        $oldDownload = PdfDownload::create([
            'user_id' => $user->id,
            'period' => '2025-05',
            'label' => 'Old',
            'filename' => 'test.pdf',
            'filepath' => 'test.pdf',
        ]);
        $oldDownload->created_at = now()->subMonths(13);
        $oldDownload->save();

        // Create a download 9 months old (should NOT be deleted)
        $nineMonthsOld = PdfDownload::create([
            'user_id' => $user->id,
            'period' => '2025-09',
            'label' => 'Recent',
            'filename' => 'nine.pdf',
            'filepath' => 'nine.pdf',
        ]);
        $nineMonthsOld->created_at = now()->subMonths(9);
        $nineMonthsOld->save();

        Artisan::call('app:clean-old-pdf-downloads');

        $this->assertDatabaseMissing('pdf_downloads', ['id' => $oldDownload->id]);
        $this->assertDatabaseHas('pdf_downloads', ['id' => $nineMonthsOld->id]);
    }
}
