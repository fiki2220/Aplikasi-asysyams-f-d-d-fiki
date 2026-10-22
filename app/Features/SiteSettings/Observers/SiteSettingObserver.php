<?php

namespace App\Features\SiteSettings\Observers;

use App\Features\SiteSettings\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SiteSettingObserver
{
    /**
     * Saat record SiteSetting dihapus → hapus juga file fisiknya
     * (gambar WebP di settings/, video di videos/).
     */
    public function deleting(SiteSetting $setting): void
    {
        $this->deleteFiles($this->toArray($setting->value));
    }

    /**
     * Saat value di-update → hapus file lama yang tidak dipakai lagi.
     */
    public function updating(SiteSetting $setting): void
    {
        $old = $this->toArray($setting->getOriginal('value'));
        $new = $this->toArray($setting->value);

        // File lama yang tidak ada di value baru → hapus
        $toDelete = array_diff($old, $new);

        $this->deleteFiles($toDelete);
    }

    /**
     * Hapus file-file dari disk public.
     */
    protected function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (
                is_string($path)
                && $path !== ''
                && Storage::disk('public')->exists($path)
            ) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Normalisasi value → array of paths.
     */
    protected function toArray($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [$value];
        }

        return is_array($value) ? $value : [];
    }
}