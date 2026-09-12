<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\TicketCategory;
use App\Services\ImageOptimizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImagesCommand extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Optimize all existing images and convert them to modern WebP format with proper dimensions';

    public function handle(): int
    {
        $this->info('Starting image optimization...');

        // 1. Optimize public/images static assets
        $staticImages = ['hero.png', 'concert.png', 'tech.png'];
        foreach ($staticImages as $imgName) {
            $pngPath = public_path('images/' . $imgName);
            $webpPath = public_path('images/' . pathinfo($imgName, PATHINFO_FILENAME) . '.webp');
            if (file_exists($pngPath)) {
                $origSize = filesize($pngPath);
                ImageOptimizerService::convertAndSaveAsWebp($pngPath, $webpPath, 1200, 80);
                $newSize = file_exists($webpPath) ? filesize($webpPath) : 0;
                $this->line("Optimized static asset: {$imgName} (" . round($origSize / 1024, 1) . " KB -> " . round($newSize / 1024, 1) . " KB WebP)");
            }
        }

        // 2. Optimize storage/app/public/events/backgrounds
        $backgroundDir = storage_path('app/public/events/backgrounds');
        if (File::isDirectory($backgroundDir)) {
            $files = File::files($backgroundDir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $origPath = $file->getRealPath();
                    $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $origPath);
                    $origSize = filesize($origPath);
                    
                    ImageOptimizerService::convertAndSaveAsWebp($origPath, $webpPath, 1200, 80);
                    $newSize = file_exists($webpPath) ? filesize($webpPath) : 0;
                    $this->line("Optimized event background: " . $file->getFilename() . " (" . round($origSize / 1024, 1) . " KB -> " . round($newSize / 1024, 1) . " KB WebP)");
                }
            }
        }

        // 3. Update database Event background_image to point to .webp if it exists
        $events = Event::whereNotNull('background_image')->get();
        foreach ($events as $event) {
            if (!str_starts_with($event->background_image, 'http')) {
                $currentRelative = $event->background_image;
                $webpRelative = preg_replace('/\.(png|jpe?g)$/i', '.webp', $currentRelative);
                if (file_exists(storage_path('app/public/' . $webpRelative))) {
                    $event->update(['background_image' => $webpRelative]);
                    $this->line("Updated Event #{$event->id} background_image to WebP: {$webpRelative}");
                }
            }
        }

        $this->info('Image optimization completed successfully!');
        return self::SUCCESS;
    }
}
