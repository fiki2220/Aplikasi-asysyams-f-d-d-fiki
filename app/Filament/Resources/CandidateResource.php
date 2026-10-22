<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CandidateResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = User::class;

    // ==========================================================
    // SETTING TAMPILAN MENU
    // ==========================================================
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Calon Peserta Didik';
    protected static ?string $modelLabel = 'Calon Siswa';
    protected static ?string $pluralModelLabel = 'Calon Peserta Didik';
    protected static ?string $navigationGroup = 'Pendaftaran (PPDB)';
    protected static ?int $navigationSort = 1;

    protected static function permission(): string
    {
        return 'spmb.manage';
    }

    // ==========================================================
    // QUERY — Hanya siswa yang belum aktif (pending)
    // ==========================================================
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'student')
            ->where('is_active', false)
            ->select([
                'id', 'name', 'email', 'role', 'is_active',
                'nisn', 'program', 'grade_level', 'gender',
                'school_origin', 'mother_name', 'father_name',
                'parent_phone', 'phone', 'birth_place', 'birth_date',
                'address', 'file_kk', 'file_akta', 'file_ijazah',
                'file_foto', 'terms_accepted_at', 'created_at', 'updated_at',
            ]);
    }

    // ==========================================================
    // FORM (Edit sederhana — data diisi dari pendaftaran)
    // ==========================================================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Biodata Pendaftar')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->disabled(),

                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP / WA')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Pendidikan & Keluarga')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\TextInput::make('program')
                            ->label('Program')
                            ->disabled(),

                        Forms\Components\TextInput::make('grade_level')
                            ->label('Jenjang / Kelas')
                            ->disabled(),

                        Forms\Components\TextInput::make('school_origin')
                            ->label('Asal Sekolah')
                            ->disabled(),

                        Forms\Components\TextInput::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => '-',
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('mother_name')
                            ->label('Nama Ibu Kandung')
                            ->disabled(),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->disabled(),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    // ==========================================================
    // INFOLIST — Modal "Lihat Detail" yang lebih lengkap
    // ==========================================================
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Biodata Calon Siswa')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('nisn')
                            ->label('NISN')
                            ->placeholder('—')
                            ->fontFamily('mono'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => '-',
                            }),
                        Infolists\Components\TextEntry::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->icon('heroicon-m-envelope'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('No. HP Santri')
                            ->placeholder('—')
                            ->copyable(),
                    ])->columns(2),

                Infolists\Components\Section::make('Akademik')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Infolists\Components\TextEntry::make('program')
                            ->label('Program')
                            ->badge()
                            ->color('info')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('grade_level')
                            ->label('Jenjang / Kelas')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('school_origin')
                            ->label('Asal Sekolah')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])->columns(2),

                Infolists\Components\Section::make('Data Orang Tua / Wali')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Infolists\Components\TextEntry::make('father_name')
                            ->label('Nama Ayah')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('mother_name')
                            ->label('Nama Ibu')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('parent_name')
                            ->label('Nama Wali')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('parent_phone')
                            ->label('No. HP Orang Tua / Wali')
                            ->copyable()
                            ->placeholder('—'),
                    ])->columns(2),

                Infolists\Components\Section::make('Dokumen Persyaratan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\TextEntry::make('file_kk')
                            ->label('Kartu Keluarga (KK)')
                            ->formatStateUsing(fn (?string $state): string => $state ? '✅ Sudah diupload' : '❌ Belum ada')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('file_akta')
                            ->label('Akta Kelahiran')
                            ->formatStateUsing(fn (?string $state): string => $state ? '✅ Sudah diupload' : '❌ Belum ada')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('file_ijazah')
                            ->label('Ijazah / SKL')
                            ->formatStateUsing(fn (?string $state): string => $state ? '✅ Sudah diupload' : '❌ Belum ada')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('file_foto')
                            ->label('Pas Foto 3x4')
                            ->formatStateUsing(fn (?string $state): string => $state ? '✅ Sudah diupload' : '❌ Belum ada')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                    ])->columns(2),

                Infolists\Components\Section::make('Informasi Pendaftaran')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Infolists\Components\TextEntry::make('terms_accepted_at')
                            ->label('Persetujuan Syarat & Ketentuan')
                            ->formatStateUsing(fn (?string $state): string => $state
                                ? '✅ Disetujui pada ' . \Carbon\Carbon::parse($state)->format('d M Y H:i')
                                : '❌ Belum disetujui')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d M Y H:i')
                            ->since(),
                    ])->columns(2),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Calon Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (User $record): ?string => $record->nisn ? 'NISN: ' . $record->nisn : null),

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
                    ->searchable(),

                Tables\Columns\TextColumn::make('grade_level')
                    ->label('Jenjang')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'L' => 'L',
                        'P' => 'P',
                        default => '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'L' => 'info',
                        'P' => 'pink',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('school_origin')
                    ->label('Asal Sekolah')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mother_name')
                    ->label('Nama Ibu')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('parent_phone')
                    ->label('HP Ortu')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->copyMessage('No HP disalin!')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // ⭐ Indikator kelengkapan dokumen (x/4)
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Dokumen')
                    ->getStateUsing(function (User $record): string {
                        $count = 0;
                        foreach (['file_kk', 'file_akta', 'file_ijazah', 'file_foto'] as $field) {
                            if (! empty($record->{$field})) {
                                $count++;
                            }
                        }
                        return $count . '/4';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === '4/4' => 'success',
                        str_starts_with($state, '0') => 'danger',
                        default => 'warning',
                    })
                    ->icon(fn (string $state): string => $state === '4/4'
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-document')
                    ->tooltip('Kelengkapan dokumen dari 4 dokumen wajib'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->date('d M Y')
                    ->description(fn (User $record): string => $record->created_at?->diffForHumans() ?? '')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->options([
                        'Tahsin' => 'Tahsin',
                        'Tilawah' => 'Tilawah',
                        'Murottal' => 'Murottal',
                        'Baca_Tulis' => 'Baca/Tulis',
                    ]),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),

                // Filter kelengkapan dokumen
                Tables\Filters\Filter::make('documents_complete')
                    ->label('Hanya Dokumen Lengkap (4/4)')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('file_kk')
                        ->whereNotNull('file_akta')
                        ->whereNotNull('file_ijazah')
                        ->whereNotNull('file_foto'))
                    ->toggle(),

                // Filter rentang tanggal daftar
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'], fn (Builder $q, $d) => $q->whereDate('created_at', '<=', $d));
                    })
                    ->label('Rentang Tanggal Daftar'),
            ])
            ->actions([
                // Lihat Detail
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->modalHeading(fn (User $record): string => 'Detail Pendaftar: ' . $record->name)
                    ->modalWidth('4xl'),

                // Terima Siswa
                Tables\Actions\Action::make('approve')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->tooltip('Terima Siswa')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Calon Siswa?')
                    ->modalDescription('Siswa akan diaktifkan dan bisa login. Data akan pindah ke menu "Data Pengguna".')
                    ->modalSubmitActionLabel('Ya, Terima')
                    ->visible(fn (): bool => auth()->user()?->hasAnyAccess(['spmb.approve', 'spmb.manage']) ?? false)
                    ->action(function (User $record) {
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('✅ Siswa Berhasil Diterima')
                            ->body("{$record->name} sekarang sudah bisa login ke dashboard.")
                            ->success()
                            ->send();
                    }),

                // Tolak / Hapus
                Tables\Actions\DeleteAction::make()
                    ->label('Tolak')
                    ->iconButton()
                    ->tooltip('Tolak & Hapus')
                    ->modalHeading('Tolak Pendaftaran?')
                    ->modalDescription(fn (User $record): string =>
                        "Apakah Anda yakin ingin menolak dan menghapus data pendaftar \"{$record->name}\"? " .
                        "Tindakan ini tidak bisa dibatalkan."
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Terima Semua Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Terima Semua Pendaftar Terpilih?')
                        ->modalDescription('Semua pendaftar yang dipilih akan diaktifkan dan bisa login.')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $user) {
                                $user->update(['is_active' => true]);
                                $count++;
                            }

                            Notification::make()
                                ->title("✅ {$count} Siswa Berhasil Diterima")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => auth()->user()?->hasAnyAccess(['spmb.approve', 'spmb.manage']) ?? false),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Tolak & Hapus Semua'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Pendaftar Baru')
            ->emptyStateDescription('Calon peserta didik akan muncul di sini setelah ada yang mendaftar.')
            ->emptyStateIcon('heroicon-o-user-plus');
    }

    // ==========================================================
    // PAGES
    // ==========================================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
            // Create/Edit dimatikan — pendaftaran via web
        ];
    }
}