<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRolePermission extends EditRecord
{
    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => RolePermissionResource::notifyPermissionUpdated()),
        ];
    }

    protected function afterSave(): void
    {
        RolePermissionResource::notifyPermissionUpdated();
    }
}
