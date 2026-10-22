<?php

namespace App\Filament\Resources\ClassGroupResource\Pages;

use App\Features\Academic\Models\ClassGroup;
use App\Filament\Resources\ClassGroupResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListClassGroups extends ListRecords
{
    protected static string $resource = ClassGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kelas')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Query dasar (dengan filter role).
     */
    protected function baseQuery(): Builder
    {
        return ClassGroupResource::getEloquentQuery();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-academic-cap')
                ->badge($this->baseQuery()->count()),

            'with_students' => Tab::make('👥 Ada Santri')
                ->badge($this->baseQuery()->has('students')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->has('students')),

            'empty' => Tab::make('⚠️ Kelas Kosong')
                ->badge($this->baseQuery()->doesntHave('students')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->doesntHave('students')),

            'no_teacher' => Tab::make('🚫 Tanpa Pengajar')
                ->badge($this->baseQuery()->whereNull('teacher_id')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNull('teacher_id')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}