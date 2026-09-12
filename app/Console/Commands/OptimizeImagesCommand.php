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

        // 5. Optimize storage/app/public/tenants/logos, avatars, wristbands, kyc, registration-proofs, invoices
        $otherDirs = [
            storage_path('app/public/tenants/logos') => ['w' => 400, 'q' => 85],
            storage_path('app/public/avatars')       => ['w' => 256, 'q' => 85],
            storage_path('app/public/wristbands/logos') => ['w' => 300, 'q' => 85],
            storage_path('app/public/wristbands/sponsors') => ['w' => 300, 'q' => 85],
            storage_path('app/public/kyc/ktp')       => ['w' => 1200, 'q' => 80],
            storage_path('app/public/kyc/face')      => ['w' => 800, 'q' => 80],
            storage_path('app/public/registration-proofs') => ['w' => 1200, 'q' => 80],
            storage_path('app/public/invoices/proofs') => ['w' => 1200, 'q' => 80],
        ];

        foreach ($otherDirs as $dir => $params) {
            if (File::isDirectory($dir)) {
                $files = File::files($dir);
                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $origPath = $file->getRealPath();
                        $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $origPath);
                        $origSize = filesize($origPath);
                        ImageOptimizerService::convertAndSaveAsWebp($origPath, $webpPath, $params['w'], $params['q']);
                        $newSize = file_exists($webpPath) ? filesize($webpPath) : 0;
                        $this->line("Optimized: " . $file->getFilename() . " (" . round($origSize / 1024, 1) . " KB -> " . round($newSize / 1024, 1) . " KB WebP)");
                    }
                }
            }
        }

        // 6. Update database Event background_image and meta_data to point to .webp if exists
        $events = Event::all();
        foreach ($events as $event) {
            $updated = false;
            if ($event->background_image && !str_starts_with($event->background_image, 'http')) {
                $webpRelative = preg_replace('/\.(png|jpe?g)$/i', '.webp', $event->background_image);
                if (file_exists(storage_path('app/public/' . $webpRelative))) {
                    $event->background_image = $webpRelative;
                    $updated = true;
                }
            }
            if (!empty($event->meta_data) && is_array($event->meta_data)) {
                $meta = $event->meta_data;
                $metaChanged = false;
                foreach (['wristband_left_logo', 'wristband_right_logo'] as $logoKey) {
                    if (!empty($meta[$logoKey]) && !str_starts_with($meta[$logoKey], 'http')) {
                        $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $meta[$logoKey]);
                        if (file_exists(storage_path('app/public/' . $webpRel))) {
                            $meta[$logoKey] = $webpRel;
                            $metaChanged = true;
                        }
                    }
                }
                if (!empty($meta['wristband_sponsor_logos']) && is_array($meta['wristband_sponsor_logos'])) {
                    foreach ($meta['wristband_sponsor_logos'] as $idx => $sLogo) {
                        if (!str_starts_with($sLogo, 'http')) {
                            $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $sLogo);
                            if (file_exists(storage_path('app/public/' . $webpRel))) {
                                $meta['wristband_sponsor_logos'][$idx] = $webpRel;
                                $metaChanged = true;
                            }
                        }
                    }
                }
                if ($metaChanged) {
                    $event->meta_data = $meta;
                    $updated = true;
                }
            }
            if ($updated) {
                $event->save();
                $this->line("Updated Event #{$event->id} image paths to WebP");
            }
        }

        // 7. Update database TicketCategory images
        $categories = TicketCategory::all();
        foreach ($categories as $cat) {
            $catUpdated = false;
            if ($cat->category_image && !str_starts_with($cat->category_image, 'http')) {
                $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $cat->category_image);
                if (file_exists(storage_path('app/public/' . $webpRel))) {
                    $cat->category_image = $webpRel;
                    $catUpdated = true;
                }
            }
            if ($cat->background_image && !str_starts_with($cat->background_image, 'http')) {
                $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $cat->background_image);
                if (file_exists(storage_path('app/public/' . $webpRel))) {
                    $cat->background_image = $webpRel;
                    $catUpdated = true;
                }
            }
            if ($catUpdated) {
                $cat->save();
                $this->line("Updated TicketCategory #{$cat->id} images to WebP");
            }
        }

        // 8. Update database Tenants logo
        $tenants = \App\Models\Tenant::whereNotNull('logo')->get();
        foreach ($tenants as $tenant) {
            if ($tenant->logo && !str_starts_with($tenant->logo, 'http')) {
                $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $tenant->logo);
                if (file_exists(storage_path('app/public/' . $webpRel))) {
                    $tenant->update(['logo' => $webpRel]);
                    $this->line("Updated Tenant #{$tenant->id} logo to WebP: {$webpRel}");
                }
            }
        }

        // 9. Update database Users avatar
        $users = \App\Models\User::whereNotNull('avatar')->get();
        foreach ($users as $user) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                $webpRel = preg_replace('/\.(png|jpe?g)$/i', '.webp', $user->avatar);
                if (file_exists(storage_path('app/public/' . $webpRel))) {
                    $user->update(['avatar' => $webpRel]);
                    $this->line("Updated User #{$user->id} avatar to WebP: {$webpRel}");
                }
            }
        }

        // 10. Update database Settings to point to .webp if it exists
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
