<?php

namespace App\Filament\Resources\TeacherAttendanceResource\Pages;

use App\Features\TeacherAttendances\Models\TeacherAttendance;
use App\Filament\Resources\TeacherAttendanceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTeacherAttendances extends ListRecords
{
    protected static string $resource = TeacherAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Absensi')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Base query (dengan filter role).
     */
    protected function baseQuery(): Builder
    {
        return TeacherAttendanceResource::getEloquentQuery();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-clipboard-document-check')
                ->badge($this->baseQuery()->count()),

            'today' => Tab::make('📅 Hari Ini')
                ->badge($this->baseQuery()->whereDate('date', today())->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('date', today())),

            'this_week' => Tab::make('🗓️ Minggu Ini')
                ->badge($this->baseQuery()
                    ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),

            'late' => Tab::make('⏰ Terlambat')
                ->badge($this->baseQuery()->where('status', 'late')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'late')),

            'alpha' => Tab::make('❌ Alpha')
                ->badge($this->baseQuery()->where('status', 'alpha')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'alpha')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}