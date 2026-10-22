<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => UserResource::canDelete($this->record)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Proteksi perubahan status aktif jika tidak diizinkan
        if (! UserResource::canManageActivation($this->record)) {
            $data['is_active'] = $this->record->is_active;
        }

        // 2. Proteksi role superadmin yang sedang login (tidak bisa diubah)
        if ($this->record->id === auth()->id() && $this->record->role === 'superadmin') {
            $data['role'] = $this->record->role;
        }

        // 3. Proteksi role superadmin dari user non-superadmin
        if (auth()->user()?->role !== 'superadmin' && $this->record->role === 'superadmin') {
            $data['role'] = $this->record->role;
            $data['is_active'] = $this->record->is_active;
        }

        // 4. Handle password baru (jika diisi)
        if (!empty($data['new_password'])) {
            $data['password'] = bcrypt($data['new_password']);
        }

        // 5. Hapus field sementara agar tidak error saat disave ke database
        unset($data['new_password'], $data['new_password_confirmation']);

        return $data;
    }
}