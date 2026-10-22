<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Features\Payments\Models\Payment;
use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pembayaran')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Tabs untuk filter cepat per status.
     */
    public function getTabs(): array
    {
        // Base query untuk menghitung badge (harus sama dengan scope di PaymentResource)
        $baseQuery = fn (): Builder => Payment::query();

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-banknotes')
                ->badge($baseQuery()->count()),

            'paid' => Tab::make('✅ Lunas')
                ->badge($baseQuery()->whereIn('status', ['paid', 'success'])->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereIn('status', ['paid', 'success'])),

            'pending' => Tab::make('⏳ Menunggu')
                ->badge($baseQuery()->where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status', 'pending')),

            'failed' => Tab::make('❌ Gagal')
                ->badge($baseQuery()->whereIn('status', ['failed', 'expired'])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereIn('status', ['failed', 'expired'])),

            'this_month' => Tab::make('📅 Bulan Ini')
                ->badge($baseQuery()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}