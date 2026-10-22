<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Proteksi: user non-superadmin tidak boleh membuat superadmin
        if (auth()->user()?->role !== 'superadmin' && ($data['role'] ?? null) === 'superadmin') {
            $data['role'] = 'guru';
        }

        // 2. Hash password baru
        if (!empty($data['new_password'])) {
            $data['password'] = bcrypt($data['new_password']);
        }

        // 3. Hapus field sementara agar tidak error saat disimpan ke database
        unset($data['new_password'], $data['new_password_confirmation']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}