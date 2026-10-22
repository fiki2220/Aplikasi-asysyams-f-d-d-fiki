<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use App\Features\Permissions\Models\RolePermission;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Str;

class ListRolePermissions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = RolePermissionResource::class;

    protected static string $view = 'filament.resources.role-permission-resource.pages.list-role-permissions';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'Hak Akses Role';
    }

    public function getHeading(): string
    {
        return 'Hak Akses Role';
    }

    public function getSubheading(): ?string
    {
        return 'Atur hak akses untuk setiap role. Perubahan berlaku setelah user reload halaman.';
    }

    public function mount(): void
    {
        $this->setPermissionsForRole('guru');
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Role')
                    ->description('Pilih role yang akan diatur. Superadmin tidak ditampilkan karena selalu punya semua akses.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(RolePermission::ROLES)
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (?string $state) => $this->setPermissionsForRole($state ?? 'guru')),

                        Forms\Components\Placeholder::make('info')
                            ->label('Informasi')
                            ->content('Perubahan hak akses berlaku setelah user melakukan reload halaman.'),
                    ])
                    ->columns(2),

                ...$this->permissionSections(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $role = $state['role'] ?? null;

        abort_unless(array_key_exists($role, RolePermission::ROLES), 403);

        $grantedCount = 0;
        $revokedCount = 0;

        // ✅ Menggunakan method baru getAllPermissions()
        foreach (RolePermission::getAllPermissions() as $permission => $label) {
            $wasAllowed = RolePermission::query()
                ->where('role', $role)
                ->where('permission', $permission)
                ->where('is_allowed', true)
                ->exists();

            $isAllowed = (bool) data_get($state, 'permissions.' . $this->permissionStateKey($permission), false);

            RolePermission::updateOrCreate(
                [
                    'role' => $role,
                    'permission' => $permission,
                ],
                [
                    'is_allowed' => $isAllowed,
                ],
            );

            if ($isAllowed && ! $wasAllowed) {
                $grantedCount++;
            } elseif (! $isAllowed && $wasAllowed) {
                $revokedCount++;
            }
        }

        $roleLabel = RolePermission::ROLES[$role] ?? $role;
        $changeSummary = [];

        if ($grantedCount > 0) {
            $changeSummary[] = "{$grantedCount} akses ditambahkan";
        }

        if ($revokedCount > 0) {
            $changeSummary[] = "{$revokedCount} akses dicabut";
        }

        $body = empty($changeSummary)
            ? 'Tidak ada perubahan pada hak akses.'
            : implode(' • ', $changeSummary) . ". Berlaku untuk role {$roleLabel}.";

        Notification::make()
            ->success()
            ->title('✅ Hak Akses Berhasil Disimpan')
            ->body($body)
            ->duration(5000)
            ->send();

        $this->setPermissionsForRole($role);
        $this->form->fill($this->data);
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('💾 Simpan Hak Akses')
                ->submit('save')
                ->color('success')
                ->size('lg')
                ->keyBindings(['mod+s']),

            Actions\Action::make('reset')
                ->label('🔄 Reset')
                ->color('gray')
                ->size('lg')
                ->requiresConfirmation()
                ->modalHeading('Reset Perubahan?')
                ->modalDescription('Semua perubahan yang belum disimpan akan hilang.')
                ->action(function () {
                    $role = $this->data['role'] ?? 'guru';
                    $this->setPermissionsForRole($role);
                    $this->form->fill($this->data);

                    Notification::make()
                        ->info()
                        ->title('Formulir direset')
                        ->body('Perubahan yang belum disimpan telah dibatalkan.')
                        ->send();
                }),
        ];
    }

    private function permissionSections(): array
    {
        $sections = [];

        foreach (RolePermission::PERMISSION_GROUPS as $group => $permissions) {
            $fields = [];

            foreach ($permissions as $permission => $label) {
                $fields[] = Forms\Components\Toggle::make('permissions.' . $this->permissionStateKey($permission))
                    ->label($label)
                    ->helperText($permission)
                    ->inline(false);
            }

            $sectionSlug = Str::slug($group);

            $sections[] = Forms\Components\Section::make($group)
                ->description($this->getSectionDescription($permissions))
                ->icon($this->getSectionIcon($group))
                ->headerActions([
                    Forms\Components\Actions\Action::make('select_all_' . $sectionSlug)
                        ->label('Pilih Semua')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->size('sm')
                        ->action(function (Forms\Set $set) use ($permissions) {
                            foreach (array_keys($permissions) as $permission) {
                                $set('permissions.' . $this->permissionStateKey($permission), true);
                            }
                        }),

                    Forms\Components\Actions\Action::make('clear_all_' . $sectionSlug)
                        ->label('Kosongkan')
                        ->icon('heroicon-m-x-circle')
                        ->color('gray')
                        ->size('sm')
                        ->action(function (Forms\Set $set) use ($permissions) {
                            foreach (array_keys($permissions) as $permission) {
                                $set('permissions.' . $this->permissionStateKey($permission), false);
                            }
                        }),
                ])
                ->schema([
                    Forms\Components\Grid::make([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])->schema($fields),
                ])
                ->collapsible();
        }

        return $sections;
    }

    private function getSectionDescription(array $permissions): string
    {
        $total = count($permissions);
        return "{$total} fitur tersedia di grup ini. Klik 'Pilih Semua' untuk mengaktifkan semuanya.";
    }

    private function getSectionIcon(string $group): string
    {
        $group = strtolower($group);

        return match (true) {
            str_contains($group, 'dashboard')                          => 'heroicon-o-chart-pie',
            str_contains($group, 'user') || str_contains($group, 'pengguna') => 'heroicon-o-users',
            str_contains($group, 'pembayaran') || str_contains($group, 'payment') => 'heroicon-o-banknotes',
            str_contains($group, 'kelas') || str_contains($group, 'class') => 'heroicon-o-academic-cap',
            str_contains($group, 'absensi') || str_contains($group, 'attendance') => 'heroicon-o-clipboard-document-check',
            str_contains($group, 'nilai') || str_contains($group, 'grade') => 'heroicon-o-document-chart-bar',
            str_contains($group, 'berita') || str_contains($group, 'post') => 'heroicon-o-newspaper',
            str_contains($group, 'pengaturan') || str_contains($group, 'setting') => 'heroicon-o-cog-6-tooth',
            str_contains($group, 'ppdb') || str_contains($group, 'spmb') => 'heroicon-o-user-plus',
            str_contains($group, 'semester')                           => 'heroicon-o-calendar-days',
            str_contains($group, 'rapor') || str_contains($group, 'report') => 'heroicon-o-document-text',
            str_contains($group, 'meeting')                            => 'heroicon-o-calendar',
            str_contains($group, 'evaluation') || str_contains($group, 'evaluasi') => 'heroicon-o-clipboard-document-list',
            str_contains($group, 'assessment')                         => 'heroicon-o-pencil-square',
            default                                                    => 'heroicon-o-key',
        };
    }

    public function setPermissionsForRole(string $role): void
    {
        abort_unless(array_key_exists($role, RolePermission::ROLES), 403);

        $allowedPermissions = RolePermission::query()
            ->where('role', $role)
            ->where('is_allowed', true)
            ->pluck('permission')
            ->all();

        $permissions = [];

        // ✅ Menggunakan method baru getAllPermissions()
        foreach (RolePermission::getAllPermissions() as $permission => $label) {
            $permissions[$this->permissionStateKey($permission)] = in_array($permission, $allowedPermissions, true);
        }

        $this->data = [
            'role' => $role,
            'permissions' => $permissions,
        ];
    }

    private function permissionStateKey(string $permission): string
    {
        return str_replace('.', '__', $permission);
    }
}