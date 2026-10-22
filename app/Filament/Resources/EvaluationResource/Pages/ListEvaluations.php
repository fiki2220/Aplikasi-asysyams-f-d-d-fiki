<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Evaluasi')
                ->icon('heroicon-o-plus'),
        ];
    }

    // ⭐ Tabs dihapus — tidak perlu filter per "Evaluasi Ke-"
    // (karena field itu sudah tidak dipakai)
}