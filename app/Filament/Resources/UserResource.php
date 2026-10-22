<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Data Pengguna';

    protected static ?string $pluralModelLabel = 'Data Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static array $userColumnCache = [];

    // ==========================================================
    // HELPER — Cek kolom database
    // ==========================================================

    public static function hasUserColumn(string $column): bool
    {
        if (! array_key_exists($column, static::$userColumnCache)) {
            static::$userColumnCache[$column] = Schema::hasColumn('users', $column);
        }

        return static::$userColumnCache[$column];
    }

    protected static function getProfilePhotoColumn(): ?string
    {
        if (static::hasUserColumn('profile_photo_path')) {
            return 'profile_photo_path';
        }

        if (static::hasUserColumn('profile_photo')) {
            return 'profile_photo';
        }

        return null;
    }

    // ==========================================================
    // ELOQUENT QUERY — Optimasi kolom yang di-query
    // ==========================================================

    /**
     * Optimasi query tabel — batasi kolom agar lebih cepat.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'id',
                'name',
                'email',
                'role',
                'is_active',
                'nisn',
                'program',
                'phone',
                'gender',
                'file_kk',
                'file_akta',
                'file_ijazah',
                'file_foto',
                'terms_accepted_at',
                'created_at',
                'updated_at',
            ]);
    }

    // ==========================================================
    // AUTHORIZATION
    // ==========================================================

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyAccess(['users.view', 'users.manage']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyAccess(['users.create', 'users.manage']) ?? false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if (! ($user?->hasAnyAccess(['users.update', 'users.manage']) ?? false)) {
            return false;
        }

        return $user->role === 'superadmin' || $record->role !== 'superadmin';
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();

        if (! ($user?->hasAnyAccess(['users.delete', 'users.manage']) ?? false)) {
            return false;
        }

        if ($record->id === $user->id) {
            return false;
        }

        return $user->role === 'superadmin' || $record->role !== 'superadmin';
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyAccess(['users.delete', 'users.manage']) ?? false;
    }

    public static function canManageActivation(?User $record = null): bool
    {
        $user = auth()->user();

        if ($user?->role !== 'superadmin') {
            return false;
        }

        return ! $record || $record->id !== $user->id;
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: INFORMASI AKUN
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Masukkan data dasar pengguna.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Muhammad Iman Saputra'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh: user@example.com'),

                        Forms\Components\Select::make('role')
                            ->label('Peran / Hak Akses')
                            ->options(fn (): array => auth()->user()?->role === 'superadmin' ? [
                                'superadmin' => 'Super Admin (Developer)',
                                'guru' => 'Guru',
                                'student' => 'Siswa (Santri)',
                            ] : [
                                'guru' => 'Guru',
                                'student' => 'Siswa (Santri)',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->disabled(fn (?User $record): bool => $record?->id === auth()->id() && $record->role === 'superadmin')
                            ->helperText(fn (?User $record): ?string => $record?->id === auth()->id() && $record->role === 'superadmin'
                                ? 'Peran akun superadmin yang sedang login tidak bisa diubah.'
                                : 'Pilih peran sesuai dengan hak akses pengguna.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // SECTION 2: KEAMANAN AKUN
                Forms\Components\Section::make('Keamanan Akun')
                    ->description('Ubah password pengguna. Kosongkan jika tidak ingin mengganti password.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->placeholder('Kosongkan jika tidak ingin mengganti password')
                            ->helperText('Minimal 8 karakter. Biarkan kosong jika tidak ingin mengubah password.')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->same('new_password_confirmation')
                            ->validationAttribute('password baru'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->placeholder('Ulangi password baru')
                            ->helperText('Harus sama dengan password baru.')
                            ->required(fn (Forms\Get $get, string $operation): bool => $operation === 'create' || filled($get('new_password')))
                            ->maxLength(255)
                            ->validationAttribute('konfirmasi password baru'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn (string $operation): bool => $operation === 'edit'),

                // SECTION 3: PROFIL PENGGUNA
                Forms\Components\Section::make('Profil Pengguna')
                    ->description('Lengkapi data profil pengguna.')
                    ->icon('heroicon-o-user-circle')
                    ->schema(static::getProfileFields())
                    ->columns(2)
                    ->visible(fn (): bool => count(static::getProfileFields()) > 0),

                // SECTION 4: DATA SISWA / SANTRI
                Forms\Components\Section::make('Data Siswa / Santri')
                    ->description('Wajib diisi jika peran adalah Siswa.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema(static::getStudentFields())
                    ->columns(2)
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'student' && count(static::getStudentFields()) > 0),

                // SECTION 5: DOKUMEN PERSYARATAN PPDB
                Forms\Components\Section::make('Dokumen Persyaratan PPDB')
                    ->description('Dokumen yang diupload saat pendaftaran. Klik file untuk preview atau download.')
                    ->icon('heroicon-o-document-text')
                    ->schema(static::getDocumentFields())
                    ->columns(2)
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'student' && count(static::getDocumentFields()) > 0)
                    ->collapsible(),

                // SECTION 6: DATA GURU
                Forms\Components\Section::make('Data Guru')
                    ->description('Isi data tambahan jika peran adalah Guru.')
                    ->icon('heroicon-o-briefcase')
                    ->schema(static::getTeacherFields())
                    ->columns(2)
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'guru' && count(static::getTeacherFields()) > 0),

                // SECTION 7: STATUS AKUN — DENGAN TOMBOL PERSETUJUAN
                Forms\Components\Section::make('Status Akun')
                    ->description('Atur status aktif pengguna dan verifikasi persetujuan syarat.')
                    ->icon('heroicon-o-signal')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Akun Aktif?')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => ! static::canManageActivation($record))
                            ->helperText(fn (?User $record): string => static::canManageActivation($record)
                                ? 'Jika dimatikan, user tidak bisa login ke sistem.'
                                : 'Status aktif hanya bisa diatur superadmin dan akun sendiri tidak bisa dinonaktifkan.'),

                        Forms\Components\Placeholder::make('terms_accepted_at_info')
                            ->label('Persetujuan Syarat & Ketentuan')
                            ->content(fn (?User $record): \Illuminate\Support\HtmlString => $record?->terms_accepted_at
                                ? new \Illuminate\Support\HtmlString(
                                    '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-100 text-green-800 text-sm font-semibold">'
                                    . '✅ Disetujui pada ' . $record->terms_accepted_at->format('d M Y H:i')
                                    . '</span>'
                                )
                                : new \Illuminate\Support\HtmlString(
                                    '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-100 text-red-800 text-sm font-semibold">'
                                    . '❌ Belum disetujui'
                                    . '</span>'
                                ))
                            ->visible(fn (): bool => static::hasUserColumn('terms_accepted_at')),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('approve_terms')
                                ->label('Tandai Sudah Disetujui')
                                ->icon('heroicon-o-check-badge')
                                ->color('success')
                                ->size('lg')
                                ->requiresConfirmation()
                                ->modalHeading('Tandai Persetujuan Syarat & Ketentuan?')
                                ->modalDescription('Pengguna ini akan ditandai sudah menyetujui Syarat & Ketentuan YPTQ Asy-Syams.')
                                ->modalSubmitActionLabel('Ya, Tandai Sekarang')
                                ->action(function (User $record, \Livewire\Component $livewire) {
                                    $record->update(['terms_accepted_at' => now()]);

                                    Notification::make()
                                        ->title('✅ Persetujuan Berhasil Ditandai')
                                        ->body('Pengguna ' . $record->name . ' sekarang tercatat sudah menyetujui Syarat & Ketentuan.')
                                        ->success()
                                        ->duration(4000)
                                        ->send();

                                    if (method_exists($livewire, 'refreshFormData')) {
                                        $livewire->refreshFormData(['terms_accepted_at']);
                                    }
                                })
                                ->visible(fn (?User $record): bool => $record && empty($record->terms_accepted_at)),
                        ])
                            ->columnSpanFull()
                            ->visible(fn (?User $record): bool => $record && static::hasUserColumn('terms_accepted_at')),
                    ])
                    ->columns(2)
                    ->visible(fn (): bool => static::hasUserColumn('is_active')),
            ]);
    }

    // ==========================================================
    // FIELD BUILDERS
    // ==========================================================

    protected static function getProfileFields(): array
    {
        $fields = [];
        $photoColumn = static::getProfilePhotoColumn();

        if ($photoColumn) {
            $fields[] = Forms\Components\FileUpload::make($photoColumn)
                ->label('Foto Profil')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatios(['1:1', '4:3', '16:9'])
                ->directory('users/profile-photos')
                ->disk('public')
                ->visibility('public')
                ->maxSize(2048)
                ->helperText('Upload foto profil. Bisa crop agar rapi.')
                ->columnSpanFull();
        }

        if (static::hasUserColumn('gender')) {
            $fields[] = Forms\Components\Select::make('gender')
                ->label('Jenis Kelamin')
                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                ->native(false)
                ->placeholder('Pilih jenis kelamin');
        }

        if (static::hasUserColumn('phone')) {
            $fields[] = Forms\Components\TextInput::make('phone')
                ->label('No. HP / WA')
                ->tel()
                ->maxLength(30)
                ->placeholder('Contoh: 081234567890');
        }

        if (static::hasUserColumn('birth_place')) {
            $fields[] = Forms\Components\TextInput::make('birth_place')
                ->label('Tempat Lahir')
                ->maxLength(100)
                ->placeholder('Contoh: Pekanbaru');
        }

        if (static::hasUserColumn('birth_date')) {
            $fields[] = Forms\Components\DatePicker::make('birth_date')
                ->label('Tanggal Lahir')
                ->native(false)
                ->displayFormat('d F Y')
                ->maxDate(now())
                ->placeholder('Pilih tanggal lahir');
        }

        if (static::hasUserColumn('address')) {
            $fields[] = Forms\Components\Textarea::make('address')
                ->label('Alamat Lengkap')
                ->rows(3)
                ->placeholder('Masukkan alamat lengkap')
                ->columnSpanFull();
        }

        return $fields;
    }

    protected static function getStudentFields(): array
    {
        $fields = [];

        if (static::hasUserColumn('nisn')) {
            $fields[] = Forms\Components\TextInput::make('nisn')
                ->label('NISN')
                ->rule('regex:/^[0-9]+$/')
                ->minLength(10)
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: 1000000001')
                ->helperText('Isi angka saja, tanpa spasi.');
        }

        if (static::hasUserColumn('program')) {
            $fields[] = Forms\Components\Select::make('program')
                ->label('Program yang Didaftarkan')
                ->options([
                    'Tahsin'     => 'Tahsin',
                    'Tilawah'    => 'Tilawah',
                    'Murottal'   => 'Murottal',
                    'Baca_Tulis' => 'Baca/Tulis Al-Qur\'an',
                ])
                ->native(false)
                ->placeholder('Pilih program')
                ->helperText('Program yang dipilih santri saat mendaftar.');
        }

        if (static::hasUserColumn('grade_level')) {
            $fields[] = Forms\Components\TextInput::make('grade_level')
                ->label('Jenjang / Kelas')
                ->maxLength(100)
                ->placeholder('Contoh: Kelas 7 / Tahfizh 1');
        }

        if (static::hasUserColumn('school_origin')) {
            $fields[] = Forms\Components\TextInput::make('school_origin')
                ->label('Asal Sekolah')
                ->maxLength(255)
                ->placeholder('Contoh: SD Negeri 01 Pekanbaru')
                ->columnSpanFull();
        }

        $parentFields = [];

        if (static::hasUserColumn('father_name')) {
            $parentFields[] = Forms\Components\TextInput::make('father_name')
                ->label('Nama Ayah')
                ->maxLength(255)
                ->placeholder('Masukkan nama ayah');
        }

        if (static::hasUserColumn('mother_name')) {
            $parentFields[] = Forms\Components\TextInput::make('mother_name')
                ->label('Nama Ibu')
                ->maxLength(255)
                ->placeholder('Masukkan nama ibu');
        }

        if (static::hasUserColumn('parent_name')) {
            $parentFields[] = Forms\Components\TextInput::make('parent_name')
                ->label('Nama Wali')
                ->maxLength(255)
                ->placeholder('Isi jika wali berbeda dari ayah / ibu');
        }

        if (static::hasUserColumn('parent_phone')) {
            $parentFields[] = Forms\Components\TextInput::make('parent_phone')
                ->label('No. HP Orang Tua / Wali')
                ->tel()
                ->maxLength(30)
                ->placeholder('Contoh: 081234567890');
        }

        if (count($parentFields) > 0) {
            $fields[] = Forms\Components\Fieldset::make('Data Orang Tua / Wali')
                ->schema($parentFields)
                ->columns(2)
                ->columnSpanFull();
        }

        return $fields;
    }

    protected static function getDocumentFields(): array
    {
        $fields = [];

        $documents = [
            'file_kk'     => ['label' => 'Kartu Keluarga (KK)', 'type' => 'pdf_image'],
            'file_akta'   => ['label' => 'Akta Kelahiran',      'type' => 'pdf_image'],
            'file_ijazah' => ['label' => 'Ijazah / SKL',        'type' => 'pdf_image'],
            'file_foto'   => ['label' => 'Pas Foto 3x4',        'type' => 'image_only'],
        ];

        foreach ($documents as $column => $config) {
            if (! static::hasUserColumn($column)) {
                continue;
            }

            $isImageOnly = $config['type'] === 'image_only';
            $folderName = str_replace('file_', '', $column);

            $fields[] = Forms\Components\FileUpload::make($column)
                ->label($config['label'])
                ->image($isImageOnly)
                ->acceptedFileTypes($isImageOnly
                    ? ['image/jpeg', 'image/png']
                    : ['application/pdf', 'image/jpeg', 'image/png'])
                ->directory("ppdb-documents/{$folderName}")
                ->disk('public')
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Format: PDF/JPG/PNG • Maks 2MB');
        }

        return $fields;
    }

    protected static function getTeacherFields(): array
    {
        $fields = [];

        if (static::hasUserColumn('nip')) {
            $fields[] = Forms\Components\TextInput::make('nip')
                ->label('NIP / Nomor Guru')
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->placeholder('Masukkan NIP / nomor guru');
        }

        if (static::hasUserColumn('position')) {
            $fields[] = Forms\Components\TextInput::make('position')
                ->label('Jabatan')
                ->maxLength(100)
                ->placeholder('Contoh: Wali Kelas / Guru Tahfizh');
        }

        if (static::hasUserColumn('subject')) {
            $fields[] = Forms\Components\TextInput::make('subject')
                ->label('Mata Pelajaran / Bidang')
                ->maxLength(100)
                ->placeholder('Contoh: Tahsin / Tahfizh / Fiqih');
        }

        return $fields;
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getTableColumns())
            ->filters(static::getTableFilters())
            ->recordClasses(fn (User $record) =>
                ! $record->is_active
                    ? 'bg-amber-50 dark:bg-amber-950/20'
                    : null
            )
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (User $record): bool => static::canEdit($record))
                    ->iconButton()
                    ->tooltip('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => static::canDelete($record))
                    ->iconButton()
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Setujui (Aktifkan)')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Pengguna Terpilih?')
                        ->modalDescription('Akun yang dipilih akan diaktifkan dan bisa login.')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => static::canManageActivation()),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Tolak (Nonaktifkan)')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Pengguna Terpilih?')
                        ->modalDescription('Akun yang dipilih akan dinonaktifkan dan tidak bisa login.')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => static::canManageActivation()),

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => static::canDeleteAny()),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10);
    }

    protected static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable()
                ->weight('semibold')
                ->description(fn (User $record): ?string => $record->nisn ? 'NISN: ' . $record->nisn : null),

            Tables\Columns\TextColumn::make('role')
                ->label('Peran')
                ->badge()
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'superadmin' => 'Super Admin',
                    'guru' => 'Guru',
                    'student' => 'Siswa',
                    default => $state ?? '-',
                })
                ->color(fn (?string $state): string => match ($state) {
                    'superadmin' => 'danger',
                    'guru' => 'warning',
                    'student' => 'success',
                    default => 'gray',
                })
                ->sortable(),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->copyable()
                ->copyMessage('Email disalin!')
                ->icon('heroicon-m-envelope')
                ->iconPosition('before')
                ->color('gray')
                ->toggleable(),

            Tables\Columns\TextColumn::make('program')
                ->label('Program')
                ->badge()
                ->color('info')
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'Baca_Tulis' => 'Baca/Tulis',
                    'Tahsin' => 'Tahsin',
                    'Tilawah' => 'Tilawah',
                    'Murottal' => 'Murottal',
                    default => '—',
                })
                ->searchable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('is_active')
                ->label('Status')
                ->badge()
                ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Pending')
                ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                ->icon(fn (bool $state): string => $state
                    ? 'heroicon-m-check-circle'
                    : 'heroicon-m-clock')
                ->sortable(),
        ];
    }

    protected static function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('program')
                ->label('Program')
                ->options([
                    'Tahsin' => 'Tahsin',
                    'Tilawah' => 'Tilawah',
                    'Murottal' => 'Murottal',
                    'Baca_Tulis' => 'Baca/Tulis',
                ]),

            Tables\Filters\TernaryFilter::make('is_active')
                ->label('Status Verifikasi')
                ->trueLabel('✅ Terverifikasi')
                ->falseLabel('⏳ Pending'),

            Tables\Filters\Filter::make('created_at')
                ->form([
                    Forms\Components\DatePicker::make('from')
                        ->label('Dari Tanggal')
                        ->native(false),
                    Forms\Components\DatePicker::make('until')
                        ->label('Sampai Tanggal')
                        ->native(false),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                })
                ->label('Rentang Tanggal Daftar'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}