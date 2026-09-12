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

        // 3. Optimize storage/app/public/settings (Logos, favicons, etc.)
        $settingsDir = storage_path('app/public/settings');
        if (File::isDirectory($settingsDir)) {
            $files = File::files($settingsDir);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $origPath = $file->getRealPath();
                    $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $origPath);
                    $origSize = filesize($origPath);
                    
                    ImageOptimizerService::convertAndSaveAsWebp($origPath, $webpPath, 400, 85);
                    $newSize = file_exists($webpPath) ? filesize($webpPath) : 0;
                    $this->line("Optimized setting asset: " . $file->getFilename() . " (" . round($origSize / 1024, 1) . " KB -> " . round($newSize / 1024, 1) . " KB WebP)");
                }
            }
        }

        // 4. Optimize storage/app/public/tickets
        $ticketDirs = [storage_path('app/public/tickets/categories'), storage_path('app/public/tickets/backgrounds')];
        foreach ($ticketDirs as $tDir) {
            if (File::isDirectory($tDir)) {
                $files = File::files($tDir);
                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $origPath = $file->getRealPath();
                        $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $origPath);
                        $origSize = filesize($origPath);
                        ImageOptimizerService::convertAndSaveAsWebp($origPath, $webpPath, 600, 80);
                        $newSize = file_exists($webpPath) ? filesize($webpPath) : 0;
                        $this->line("Optimized ticket asset: " . $file->getFilename() . " (" . round($origSize / 1024, 1) . " KB -> " . round($newSize / 1024, 1) . " KB WebP)");
                    }
                }
            }
        }

        // 5. Update database Event background_image to point to .webp if it exists
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

        // 6. Update database Settings to point to .webp if it exists
        $settings = \App\Models\Setting::whereIn('key', ['app_logo', 'app_favicon', 'app_icon', 'wristband_league_logo'])->get();
        foreach ($settings as $setting) {
            if ($setting->value && !str_starts_with($setting->value, 'http')) {
                $webpVal = preg_replace('/\.(png|jpe?g)$/i', '.webp', $setting->value);
                if (file_exists(storage_path('app/public/' . $webpVal))) {
                    $setting->update(['value' => $webpVal]);
                    $this->line("Updated Setting {$setting->key} to WebP: {$webpVal}");
                }
            }
        }
        \Illuminate\Support\Facades\Cache::forget('public_settings_map');

        $this->info('Image optimization completed successfully!');
        return self::SUCCESS;
    }
}
