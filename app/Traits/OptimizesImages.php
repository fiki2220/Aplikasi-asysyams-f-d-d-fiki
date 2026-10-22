<?php

namespace App\Traits;

use App\Services\ImageOptimizerService;

trait OptimizesImages
{
    /**
     * ⭐ Boot trait — auto-convert setelah model di-save.
     */
    public static function bootOptimizesImages(): void
    {
        static::saved(function ($model) {
            $model->optimizeImageColumns();
        });
    }

    /**
     * Convert semua kolom gambar ke WebP.
     */
    public function optimizeImageColumns(): void
    {
        $config = $this->optimizableImageColumns();
        if (empty($config)) return;

        $service = app(ImageOptimizerService::class);
        $dirty   = false;

        foreach ($config as $column => $options) {
            $value = $this->{$column};
            if (! $value) continue;

            $newValue = $service->processUploadedFile(
                $value,
                $options['disk']      ?? 'public',
                $options['quality']   ?? 85,
                $options['max_width'] ?? null
            );

            if ($newValue !== $value) {
                $this->{$column} = $newValue;
                $dirty = true;
            }
        }

        // Save tanpa trigger event lagi
        if ($dirty) {
            $this->saveQuietly();
        }
    }

    /**
     * Return array config kolom gambar.
     * Format: ['column_name' => ['quality' => 85, 'max_width' => 1920]]
     */
    protected function optimizableImageColumns(): array
    {
        return property_exists($this, 'optimizableImages')
            ? $this->optimizableImages
            : [];
    }
}