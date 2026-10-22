<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use App\Filament\Resources\CandidateResource;
use App\Models\User;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCandidates extends ListRecords
{
    protected static string $resource = CandidateResource::class;

    /**
     * Tidak ada tombol "New" — pendaftaran dilakukan via halaman web publik.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Tabs untuk filter cepat per kategori.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-user-group')
                ->badge(static::baseQuery()->count()),

            'complete' => Tab::make('📄 Dokumen Lengkap')
                ->badge(static::baseQuery()
                    ->whereNotNull('file_kk')
                    ->whereNotNull('file_akta')
                    ->whereNotNull('file_ijazah')
                    ->whereNotNull('file_foto')
                    ->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotNull('file_kk')
                    ->whereNotNull('file_akta')
                    ->whereNotNull('file_ijazah')
                    ->whereNotNull('file_foto')),

            'incomplete' => Tab::make('⚠️ Dokumen Belum Lengkap')
                ->badge(static::baseQuery()
                    ->where(fn (Builder $q) => $q
                        ->whereNull('file_kk')
                        ->orWhereNull('file_akta')
                        ->orWhereNull('file_ijazah')
                        ->orWhereNull('file_foto'))
                    ->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where(fn (Builder $q) => $q
                        ->whereNull('file_kk')
                        ->orWhereNull('file_akta')
                        ->orWhereNull('file_ijazah')
                        ->orWhereNull('file_foto'))),

            'recent' => Tab::make('🆕 7 Hari Terakhir')
                ->badge(static::baseQuery()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('created_at', '>=', now()->subDays(7))),
        ];
    }

    /**
     * Tab default saat halaman pertama dibuka.
     */
    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    /**
     * Query dasar untuk hitung badge di tabs.
     * Sama dengan filter di CandidateResource::getEloquentQuery().
     */
    protected static function baseQuery(): Builder
    {
        return User::query()
            ->where('role', 'student')
            ->where('is_active', false);
    }
}