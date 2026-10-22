<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use App\Features\Grades\Models\Assessment;
use App\Filament\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAssessments extends ListRecords
{
    protected static string $resource = AssessmentResource::class;

    // ==========================================================
    // HEADER ACTIONS
    // ==========================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Penilaian')
                ->icon('heroicon-o-plus'),
        ];
    }

    // ==========================================================
    // BASE QUERY
    // ==========================================================

    protected function baseQuery(): Builder
    {
        return AssessmentResource::getEloquentQuery();
    }

    // ==========================================================
    // TABS
    // ==========================================================

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-document-check')
                ->badge($this->baseQuery()->count()),

            'today' => Tab::make('Hari Ini')
                ->icon('heroicon-o-calendar')
                ->badge($this->baseQuery()->whereDate('created_at', today())->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('created_at', today())),

            'this_month' => Tab::make('Bulan Ini')
                ->icon('heroicon-o-calendar-days')
                ->badge($this->baseQuery()
                    ->where('month', now()->month)
                    ->where('year', now()->year)
                    ->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('month', now()->month)
                          ->where('year', now()->year)),

            'ziyadah' => Tab::make('Ziyadah')
                ->icon('heroicon-m-arrow-trending-up')
                ->badge($this->baseQuery()->where('assessment_type', 'like', '%ziyadah%')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assessment_type', 'like', '%ziyadah%')),

            'murojaah' => Tab::make('Murojaah')
                ->icon('heroicon-m-arrow-path')
                ->badge($this->baseQuery()->where('assessment_type', 'like', '%murojaah%')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assessment_type', 'like', '%murojaah%')),

            // ⭐ TAB MUROTTAL — BARU DITAMBAHKAN
            'murottal' => Tab::make('Murottal')
                ->icon('heroicon-m-musical-note')
                ->badge($this->baseQuery()->where('assessment_type', 'like', '%murottal%')->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assessment_type', 'like', '%murottal%')),

            'tahsin' => Tab::make('Tahsin')
                ->icon('heroicon-m-academic-cap')
                ->badge($this->baseQuery()->where('assessment_type', 'like', '%tahsin%')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assessment_type', 'like', '%tahsin%')),

            'tilawah' => Tab::make('Tilawah')
                ->icon('heroicon-m-book-open')
                ->badge($this->baseQuery()->where('assessment_type', 'like', '%tilawah%')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('assessment_type', 'like', '%tilawah%')),
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
        return 'Input Penilaian Santri';
    }

    // Subheading sengaja tidak ditampilkan
    public function getSubheading(): ?string
    {
        return null;
    }
}