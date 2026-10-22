<?php

namespace App\Filament\Resources\MeetingResource\Pages;

use App\Filament\Resources\MeetingResource;
use App\Features\Meetings\Models\Meeting;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMeetings extends ListRecords
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pertemuan')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        // Base query (dengan filter role guru)
        $base = fn (): Builder => MeetingResource::getEloquentQuery();

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-calendar-days')
                ->badge($base()->count()),

            'today' => Tab::make('📅 Hari Ini')
                ->badge($base()->whereDate('date', today())->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereDate('date', today())),

            'this_week' => Tab::make('🗓️ Minggu Ini')
                ->badge($base()
                    ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),

            'this_month' => Tab::make('📆 Bulan Ini')
                ->badge($base()
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year)),

            'no_attendance' => Tab::make('⚠️ Belum Ada Absensi')
                ->badge($base()->doesntHave('attendances')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->doesntHave('attendances')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}