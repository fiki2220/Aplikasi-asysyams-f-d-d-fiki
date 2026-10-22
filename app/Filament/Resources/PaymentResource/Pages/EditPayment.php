<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Features\Payments\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    // ==========================================================
    // HEADER ACTIONS
    // ==========================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_paid')
                ->label('Tandai Lunas')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Tandai Pembayaran Lunas?')
                ->modalDescription(fn () => "Pembayaran {$this->record->order_id} sebesar " .
                    'Rp ' . number_format((float) $this->record->amount, 0, ',', '.') .
                    " akan ditandai LUNAS.")
                ->modalSubmitActionLabel('Ya, Tandai Lunas')
                ->action(function () {
                    $this->record->update([
                        'status' => PaymentStatus::Paid->value,
                    ]);

                    Notification::make()
                        ->title('✅ Pembayaran Ditandai Lunas')
                        ->body("ID: {$this->record->order_id}")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                })
                ->visible(fn (): bool =>
                    ! in_array($this->record->status, [
                        PaymentStatus::Paid->value,
                        'success',
                    ], true)
                ),

            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Pembayaran?')
                ->modalDescription(fn () => "Data pembayaran dengan ID \"{$this->record->order_id}\" " .
                    "akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.")
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    // ==========================================================
    // FORM ACTIONS
    // ==========================================================

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('💾 Simpan Perubahan')
            ->color('success');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal')
            ->url($this->getResource()::getUrl('index'));
    }

    // ==========================================================
    // NOTIFIKASI SETELAH SAVE
    // ==========================================================

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('✅ Perubahan Berhasil Disimpan')
            ->body("Data pembayaran ID: {$this->record->order_id} telah diperbarui.")
            ->duration(3000);
    }

    // ==========================================================
    // REDIRECT
    // ==========================================================

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // ==========================================================
    // TITLE
    // ==========================================================

    public function getTitle(): string
    {
        return "Edit Pembayaran • {$this->record->order_id}";
    }

    // ⭐ getBreadcrumbs() dihapus — biarkan Filament generate otomatis
}