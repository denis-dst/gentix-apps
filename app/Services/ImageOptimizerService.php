<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Upload an image, resize if necessary, and save as WebP format.
     *
     * @param UploadedFile $file
     * @param string $directory Relative directory in public storage (e.g. 'events/backgrounds')
     * @param int $maxWidth Max width to downscale large images (default: 1200)
     * @param int $quality WebP quality 0-100 (default: 80)
     * @return string Stored relative path (e.g. 'events/backgrounds/abc.webp')
     */
    public static function uploadAndOptimize(UploadedFile $file, string $directory = 'events/backgrounds', int $maxWidth = 1200, int $quality = 80): string
    {
        $filename = Str::random(40) . '.webp';
        $relativeDir = trim($directory, '/');
        $targetDir = storage_path('app/public/' . $relativeDir);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        $sourcePath = $file->getRealPath();

        $converted = self::convertAndSaveAsWebp($sourcePath, $targetPath, $maxWidth, $quality);

        if ($converted) {
            return $relativeDir . '/' . $filename;
        }

        // Fallback: standard store if GD / conversion fails
        return $file->store($relativeDir, 'public');
    }

    /**
     * Convert an image file to WebP and downscale if it exceeds max width.
     */
    public static function convertAndSaveAsWebp(string $sourcePath, string $targetPath, int $maxWidth = 1200, int $quality = 80): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $mime = $imageInfo['mime'];
        $srcImg = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default => false,
        };

        if (!$srcImg) {
            return false;
        }

        $origW = imagesx($srcImg);
        $origH = imagesy($srcImg);

        if ($origW > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int) round($origH * ($maxWidth / $origW));
            $dstImg = imagecreatetruecolor($newW, $newH);

            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($srcImg);
            $srcImg = $dstImg;
        }

        $result = imagewebp($srcImg, $targetPath, $quality);
        imagedestroy($srcImg);

        return (bool) $result;
    }

    /**
     * Optimize existing file in storage and generate .webp sibling if needed.
     */
    public static function optimizeFile(string $fullPath, int $maxWidth = 1200, int $quality = 80): ?string
    {
        if (!file_exists($fullPath)) {
            return null;
        }

        $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $fullPath);
        if (self::convertAndSaveAsWebp($fullPath, $webpPath, $maxWidth, $quality)) {
            return $webpPath;
        }

        return null;
    }
}
