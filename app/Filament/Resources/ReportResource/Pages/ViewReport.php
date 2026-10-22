<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Features\Academic\Models\ClassGroup;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali ke Daftar')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ReportResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return "Rapor Kelas • {$this->record->name}";
    }

    // ⭐ Subheading dihapus — tidak perlu
    public function getSubheading(): ?string
    {
        return null;
    }
}