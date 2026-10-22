<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Features\Grades\Models\Grade;
use App\Filament\Resources\GradeResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListGrades extends ListRecords
{
    protected static string $resource = GradeResource::class;

    // ==========================================================
    // HEADER ACTIONS
    // ==========================================================
    protected function getHeaderActions(): array
    {
        return [];
    }

    // ==========================================================
    // BASE QUERY
    // ==========================================================

    protected function baseQuery(): Builder
    {
        return GradeResource::getEloquentQuery();
    }

    // ==========================================================
    // TABS — Filter per semester / per jenis nilai
    // ==========================================================

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge(
                    Grade::query()
                        ->selectRaw('COUNT(DISTINCT CONCAT(user_id, "-", semester_id)) as total')
                        ->value('total') ?? 0
                ),

            'semester_aktif' => Tab::make('Semester Aktif')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(function (Builder $query) {
                    $activeSemesterId = \App\Features\Academic\Models\Semester::where('is_active', true)->value('id');

                    return $activeSemesterId
                        ? $query->where('grades.semester_id', $activeSemesterId)
                        : $query;
                }),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    // ==========================================================
    // TITLE
    // ==========================================================

    public function getTitle(): string
    {
        return 'Rekapan Buku Harian Santri';
    }

    // Subheading sengaja tidak ditampilkan
    public function getSubheading(): ?string
    {
        return null;
    }
}