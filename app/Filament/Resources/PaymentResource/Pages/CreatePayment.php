<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    // ==========================================================
    // MUTATE — Pastikan order_id unik
    // ==========================================================

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['order_id'])) {
            $data['order_id'] = 'MANUAL-' . Str::uuid()->toString();
        }

        return $data;
    }

    // ==========================================================
    // REDIRECT
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ==========================================================
    // NOTIFIKASI
    // ==========================================================

    protected function getCreatedNotification(): ?Notification
    {
        $orderId = $this->record->order_id ?? '-';
        $amount = 'Rp ' . number_format((float) ($this->record->amount ?? 0), 0, ',', '.');
        $studentName = $this->record->student?->name ?? '-';

        return Notification::make()
            ->success()
            ->title('✅ Pembayaran Berhasil Dicatat')
            ->body("ID: {$orderId} • Siswa: {$studentName} • Nominal: {$amount}")
            ->duration(5000);
    }

    // ==========================================================
    // FORM ACTIONS
    // ==========================================================

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('💾 Simpan Pembayaran')
            ->color('success');
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->url($this->getResource()::getUrl('index'));
    }

    // ==========================================================
    // TITLE
    // ==========================================================

    public function getTitle(): string
    {
        return 'Catat Pembayaran Baru';
    }

    // ⭐ getBreadcrumbs() dihapus
}