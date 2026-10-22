<?php

namespace App\Filament\Resources\CharacterAssessmentResource\Pages;

use App\Filament\Resources\CharacterAssessmentResource;
use App\Features\Grades\Models\CharacterAssessment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCharacterAssessments extends ListRecords
{
    protected static string $resource = CharacterAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Penilaian')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function baseQuery(): Builder
    {
        return CharacterAssessmentResource::getEloquentQuery();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-sparkles')
                ->badge($this->baseQuery()->count()),

            'excellent' => Tab::make('⭐ Rata-rata ≥ 90')
                ->badge($this->baseQuery()->whereRaw('(adab_islam + kerjasama + tanggung_jawab + kedisiplinan + kejujuran) / 5 >= 90')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereRaw('(adab_islam + kerjasama + tanggung_jawab + kedisiplinan + kejujuran) / 5 >= 90')),

            'below_80' => Tab::make('⚠️ Ada Nilai < 80')
                ->badge($this->baseQuery()
                    ->where(function ($q) {
                        $q->where('adab_islam', '<', 80)
                          ->orWhere('kerjasama', '<', 80)
                          ->orWhere('tanggung_jawab', '<', 80)
                          ->orWhere('kedisiplinan', '<', 80)
                          ->orWhere('kejujuran', '<', 80);
                    })
                    ->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where(function ($q) {
                        $q->where('adab_islam', '<', 80)
                          ->orWhere('kerjasama', '<', 80)
                          ->orWhere('tanggung_jawab', '<', 80)
                          ->orWhere('kedisiplinan', '<', 80)
                          ->orWhere('kejujuran', '<', 80);
                    })),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}