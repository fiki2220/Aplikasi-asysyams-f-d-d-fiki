<?php

namespace App\Filament\Resources\SiteSettingResource\Pages;

use App\Filament\Resources\SiteSettingResource;
use App\Features\SiteSettings\Models\SiteSetting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditSiteSetting extends EditRecord
{
    protected static string $resource = SiteSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // ==========================================================
    // Saat halaman Edit dibuka
    // ==========================================================
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['key'] = $this->record->key;
        return SiteSettingResource::mutateFormDataBeforeFill($data);
    }

    // ==========================================================
    // Saat tombol Save ditekan
    // ==========================================================
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['key'])) {
            $data['key'] = $this->record->key;
        }
        return SiteSettingResource::mergeVirtualFieldsToValue($data);
    }

    // ==========================================================
    // ⭐ SETELAH SAVE — Handle MULTIPLE video WebM
    // ==========================================================
    protected function afterSave(): void
    {
        if ($this->record->key !== 'hero_bg') {
            return;
        }

        $disk = Storage::disk('public');

        // Ambil state value_video_bg (multiple → array)
        $newPaths = $this->data['value_video_bg'] ?? [];
        if (! is_array($newPaths)) {
            $newPaths = $newPaths ? [$newPaths] : [];
        }
        $newPaths = array_values(array_filter($newPaths));

        // Ambil record lama
        $heroVideo = SiteSetting::where('key', 'hero_video')->first();

        $oldPaths = [];
        if ($heroVideo) {
            $oldVal = $heroVideo->value;
            if (is_array($oldVal)) {
                $oldPaths = array_values(array_filter($oldVal));
            } elseif (is_string($oldVal) && $oldVal !== '') {
                $decoded = json_decode($oldVal, true);
                $oldPaths = is_array($decoded) ? $decoded : [$oldVal];
            }
        }

        // ── Hapus file lama yang tidak ada di list baru ──
        $removed = array_diff($oldPaths, $newPaths);
        foreach ($removed as $path) {
            if (is_string($path) && $path !== '' && $disk->exists($path)) {
                $disk->delete($path);
            }
        }

        if (! empty($newPaths)) {
            // Ada video → update / create record hero_video
            SiteSetting::updateOrCreate(
                ['key' => 'hero_video'],
                ['value' => $newPaths]
            );
        } else {
            // Kosong → hapus record hero_video
            if ($heroVideo) {
                $heroVideo->delete();
            }
        }
    }
}