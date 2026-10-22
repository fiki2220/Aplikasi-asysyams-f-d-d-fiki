<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SemesterResource\Pages;
use App\Features\Academic\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SemesterResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Semester::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Periode Semester';

    protected static ?string $modelLabel = 'Periode Semester';

    protected static ?string $pluralModelLabel = 'Periode Semester';

    protected static ?int $navigationSort = 2;

    protected static function permission(): string
    {
        return 'semesters.manage';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Periode')
                    ->description('Data utama periode semester.')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Semester')
                            ->placeholder('Contoh: Ganjil 2026/2027')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->afterOrEqual('start_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Biaya & Status')
                    ->description('Atur biaya SPP dan status aktif semester.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('tuition_fee')
                            ->label('Biaya SPP Bulanan')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->helperText('Masukkan angka tanpa titik. Contoh: 750000'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Set Semester Ini Aktif?')
                            ->helperText('⚠️ Hanya satu semester yang boleh aktif. Mengaktifkan ini akan menonaktifkan semester lain.')
                            ->default(false)
                            ->live(),
                    ])
                    ->columns(2),
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
                    ->label('Periode')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Semester $record): ?string => $record->is_active
                        ? '🟢 Sedang Berlangsung'
                        : null),

                Tables\Columns\TextColumn::make('tuition_fee')
                    ->label('SPP')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignRight()
                    ->weight('medium'),

                // Durasi otomatis (hitung bulan)
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi')
                    ->getStateUsing(function (Semester $record): string {
                        if (! $record->start_date || ! $record->end_date) {
                            return '—';
                        }

                        $months = Carbon::parse($record->start_date)
                            ->diffInMonths(Carbon::parse($record->end_date));

                        return $months . ' bulan';
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn (bool $state): string => $state
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-x-circle')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('✅ Aktif')
                    ->falseLabel('⚪ Nonaktif'),

                Tables\Filters\Filter::make('current_year')
                    ->label('Semester Tahun Ini')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereYear('start_date', now()->year))
                    ->toggle(),
            ])
            ->actions([
                // Aktifkan Semester (otomatis matikan yang lain)
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-badge')
                    ->iconButton()
                    ->tooltip('Jadikan Semester Aktif')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Semester Ini?')
                    ->modalDescription('Semester lain yang aktif akan otomatis dinonaktifkan.')
                    ->action(function (Semester $record) {
                        Semester::where('id', '!=', $record->id)->update(['is_active' => false]);
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('✅ Semester Berhasil Diaktifkan')
                            ->body("Semester \"{$record->name}\" sekarang menjadi periode aktif.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Semester $record): bool => ! $record->is_active),

                // Edit
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),

                // Duplikat
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->iconButton()
                    ->tooltip('Duplikat Semester')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Duplikat Semester Ini?')
                    ->modalDescription('Semester baru akan dibuat dengan data yang sama.')
                    ->action(function (Semester $record) {
                        $new = $record->replicate();
                        $new->name = $record->name . ' (Copy)';
                        $new->is_active = false;
                        $new->save();

                        Notification::make()
                            ->title('✅ Semester Berhasil Diduplikat')
                            ->body("Semester baru: \"{$new->name}\"")
                            ->success()
                            ->send();
                    }),

                // Hapus
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Semester?')
                    ->modalDescription(fn (Semester $record): string =>
                        "Apakah Anda yakin ingin menghapus semester \"{$record->name}\"? Tindakan ini tidak dapat dibatalkan."
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Semester Terpilih?')
                        ->modalDescription('Jika ada lebih dari 1 dipilih, hanya yang terakhir akan diaktifkan.')
                        ->action(function ($records) {
                            Semester::query()->update(['is_active' => false]);
                            $lastRecord = $records->last();
                            if ($lastRecord) {
                                $lastRecord->update(['is_active' => true]);
                            }

                            Notification::make()
                                ->title('✅ Semester Berhasil Diaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->recordClasses(fn (Semester $record): ?string =>
                $record->is_active ? 'bg-green-50 dark:bg-green-950/20' : null
            )
            ->emptyStateHeading('Belum Ada Periode Semester')
            ->emptyStateDescription('Tambahkan periode semester pertama untuk memulai.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [
            SemesterResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}