<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Features\Permissions\Models\RolePermission;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class RolePermissionResource extends Resource
{
    protected static ?string $model = RolePermission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Hak Akses Role';
    protected static ?string $navigationGroup = 'Manajemen Akses';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Hak Akses Role';
    protected static ?string $pluralModelLabel = 'Hak Akses Role';

    /**
     * ✅ Menu hanya muncul di sidebar jika yang login adalah Superadmin.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

    /**
     * ✅ Kunci akses: Hanya Superadmin yang boleh membuka halaman ini.
     * Jika Admin mencoba masuk, mereka akan ditolak (seperti yang Anda alami).
     */
    public static function canViewAny(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolePermissions::route('/'),
        ];
    }
}