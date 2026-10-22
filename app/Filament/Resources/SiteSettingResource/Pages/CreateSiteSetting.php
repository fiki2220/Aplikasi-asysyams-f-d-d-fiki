<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Features\SiteSettings\Models\SiteSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateSiteSetting extends CreateRecord
{
    protected static string $resource = SiteSettingResource::class;

    // ==========================================================
    // ⭐ SETELAH CREATE — Handle MULTIPLE video WebM
    // ==========================================================
    protected function afterCreate(): void
    {
        if ($this->record->key !== 'hero_bg') {
            return;
        }

        $newPaths = $this->data['value_video_bg'] ?? [];
        if (! is_array($newPaths)) {
            $newPaths = $newPaths ? [$newPaths] : [];
        }
        $newPaths = array_values(array_filter($newPaths));

        if (! empty($newPaths)) {
            SiteSetting::updateOrCreate(
                ['key' => 'hero_video'],
                ['value' => $newPaths]
            );
        }
    }
}