<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Pengguna')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Ambil semua count langsung dari database (Real-time).
     */
    protected function getCounts(): array
    {
        // 1 query untuk count per role
        $byRole = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // 1 query untuk count pending
        $pending = User::where('is_active', false)->count();

        // Total semua
        $all = array_sum($byRole);

        return [
            'all' => $all,
            'student' => $byRole['student'] ?? 0,
            'guru' => $byRole['guru'] ?? 0,
            'admin' => $byRole['admin'] ?? 0, 
            'superadmin' => $byRole['superadmin'] ?? 0,
            'pending' => $pending,
        ];
    }

    public function getTabs(): array
    {
        $counts = $this->getCounts();

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-user-group')
                ->badge($counts['all']),

            'student' => Tab::make('Siswa')
                ->icon('heroicon-o-academic-cap')
                ->badge($counts['student'])
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'student')),

            'guru' => Tab::make('Guru')
                ->icon('heroicon-o-briefcase')
                ->badge($counts['guru'])
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'guru')),

            'admin' => Tab::make('Admin')
                ->icon('heroicon-o-user')
                ->badge($counts['admin'])
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin')),

            'superadmin' => Tab::make('Super Admin')
                ->icon('heroicon-o-shield-check')
                ->badge($counts['superadmin'])
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'superadmin')),

            'pending' => Tab::make('⏳ Pending')
                ->icon('heroicon-o-clock')
                ->badge($counts['pending'])
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}