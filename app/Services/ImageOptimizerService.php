<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizerService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * ⭐ Konversi gambar ke WebP.
     *
     * @param  string   $path       Path relatif di storage (mis. "settings/xxx.png")
     * @param  string   $disk       Disk storage (default: public)
     * @param  int      $quality    Kualitas WebP (1-100, default: 85)
     * @param  int|null $maxWidth   Max lebar (px). Null = tidak di-resize
     * @return string   Path baru (WebP) atau path lama kalau tidak perlu convert
     */
    public function convertToWebp(
        string $path,
        string $disk = 'public',
        int $quality = 85,
        ?int $maxWidth = null
    ): string {
        // 1. Cek file ada
        if (! Storage::disk($disk)->exists($path)) {
            return $path;
        }

        // 2. Skip kalau sudah WebP
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'webp') {
            return $path;
        }

        // 3. Skip kalau bukan gambar yang didukung
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'bmp'], true)) {
            return $path;
        }

        try {
            // 4. Baca gambar
            $fullPath = Storage::disk($disk)->path($path);
            $image    = $this->manager->read($fullPath);

            // 5. Resize kalau lebar melebihi maxWidth
            if ($maxWidth && $image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            // 6. Encode ke WebP
            $webpContent = (string) $image->toWebp($quality);

            // 7. Generate path baru dengan .webp
            $newPath = preg_replace('/\.[^.]+$/', '.webp', $path);

            // 8. Simpan WebP baru
            Storage::disk($disk)->put($newPath, $webpContent);

            // 9. Hapus file lama kalau berbeda
            if ($newPath !== $path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }

            return $newPath;
        } catch (\Throwable $e) {
            \Log::warning('WebP conversion failed: ' . $e->getMessage(), [
                'path' => $path,
                'disk' => $disk,
            ]);

            return $path;
        }
    }

    /**
     * ⭐ Process gambar uploaded dari Filament.
     * Filament simpan sebagai string path atau array.
     */
    public function processUploadedFile($value, string $disk = 'public', int $quality = 85, ?int $maxWidth = null)
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->processUploadedFile($v, $disk, $quality, $maxWidth), $value);
        }

        if (! is_string($value) || $value === '') {
            return $value;
        }

        return $this->convertToWebp($value, $disk, $quality, $maxWidth);
    }
}