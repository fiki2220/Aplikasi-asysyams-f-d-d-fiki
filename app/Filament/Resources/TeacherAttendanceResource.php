<?php

namespace App\Filament\Resources;

use App\Features\TeacherAttendances\Models\TeacherAttendance;
use App\Filament\Resources\TeacherAttendanceResource\Pages;
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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeacherAttendanceResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = TeacherAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Absensi Ustad';
    protected static ?string $modelLabel = 'Absensi Ustad';
    protected static ?string $pluralModelLabel = 'Absensi Ustad';
    protected static ?int $navigationSort = 3;

    protected static function permission(): string
    {
        return 'teacher-attendances.manage';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                Auth::user()?->role !== 'superadmin',
                fn (Builder $query): Builder => $query->where('user_id', Auth::id())
            );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Absensi')
                    ->description('Detail kehadiran ustad/guru.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Guru / Ustad')
                            ->options(fn (): array => User::query()
                                ->where('role', 'guru')->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()->preload()->required()->native(false),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')->default(now())->required()->native(false)
                            ->displayFormat('d F Y')->maxDate(now()),

                        Forms\Components\DateTimePicker::make('check_in_at')
                            ->label('Waktu Check In')
                            ->default(fn (string $operation) => $operation === 'create' ? now() : null)
                            ->native(false)->displayFormat('d F Y H:i')->seconds(false),

                        Forms\Components\DateTimePicker::make('check_out_at')
                            ->label('Waktu Check Out')
                            ->native(false)->displayFormat('d F Y H:i')->seconds(false)
                            ->afterOrEqual('check_in_at'),

                        Forms\Components\Select::make('status')
                            ->label('Status Kehadiran')
                            ->options(TeacherAttendance::STATUSES)
                            ->default('present')->required()->native(false),

                        Forms\Components\Select::make('created_by')
                            ->label('Dibuat Oleh')->relationship('creator', 'name')
                            ->default(fn () => Auth::id())->disabled()->dehydrated()->nullable(),

                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')->placeholder('Catatan tambahan (opsional)')
                            ->rows(3)->maxLength(1000)->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        // (Sama seperti sebelumnya, tidak ada perubahan)
        return $infolist->schema([
            Infolists\Components\Section::make('Data Kehadiran')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Infolists\Components\TextEntry::make('user.name')->label('Guru / Ustad')->weight('bold')->icon('heroicon-m-user-circle'),
                    Infolists\Components\TextEntry::make('date')->label('Tanggal')->date('d F Y'),
                    Infolists\Components\TextEntry::make('check_in_at')->label('Check In')->dateTime('H:i')->placeholder('—')->icon('heroicon-m-arrow-right-end-on-rectangle')->color('success'),
                    Infolists\Components\TextEntry::make('check_out_at')->label('Check Out')->dateTime('H:i')->placeholder('—')->icon('heroicon-m-arrow-left-start-on-rectangle')->color('danger'),
                    Infolists\Components\TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn (?string $state): string => TeacherAttendance::STATUSES[$state] ?? $state)
                        ->color(fn (?string $state): string => match ($state) {
                            'present' => 'success', 'late' => 'warning', 'permission' => 'info', 'sick' => 'gray', 'alpha' => 'danger', default => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('creator.name')->label('Dicatat Oleh')->placeholder('—'),
                    Infolists\Components\TextEntry::make('note')->label('Catatan')->placeholder('—')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('created_at')->label('Waktu Dibuat')->dateTime('d M Y H:i')->since()->color('gray'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date('d M Y')
                    ->description(fn (TeacherAttendance $record): string => $record->date?->diffForHumans() ?? '')
                    ->sortable()->weight('medium'),

                Tables\Columns\TextColumn::make('user.name')->label('Nama Guru')
                    ->searchable()->sortable()->weight('semibold')->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('check_in_at')->label('Check In')
                    ->dateTime('H:i')->placeholder('—')->icon('heroicon-m-arrow-right-end-on-rectangle')->color('success')->sortable(),

                Tables\Columns\TextColumn::make('check_out_at')->label('Check Out')
                    ->dateTime('H:i')->placeholder('—')->icon('heroicon-m-arrow-left-start-on-rectangle')->color('danger')->sortable(),

                // ✅ PERBAIKAN DURASI: Menampilkan status "Belum Check Out" alih-alih 0m
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi')
                    ->getStateUsing(function (TeacherAttendance $record): string {
                        if (! $record->check_in_at) return '—';
                        if (! $record->check_out_at) return 'Belum Check Out';
                        
                        $minutes = Carbon::parse($record->check_in_at)->diffInMinutes(Carbon::parse($record->check_out_at));
                        $hours = floor($minutes / 60);
                        $mins = $minutes % 60;

                        return $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'Belum Check Out' ? 'warning' : 'info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (?string $state): string => TeacherAttendance::STATUSES[$state] ?? $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'present' => 'success', 'late' => 'warning', 'permission' => 'info', 'sick' => 'gray', 'alpha' => 'danger', default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'present' => 'heroicon-m-check-circle', 'late' => 'heroicon-m-clock', 'permission' => 'heroicon-m-document-text', 'sick' => 'heroicon-m-heart', 'alpha' => 'heroicon-m-x-circle', default => 'heroicon-m-question-mark-circle',
                    })->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([Forms\Components\DatePicker::make('date')->label('Tanggal')->native(false)])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['date'] ?? null, fn (Builder $q, string $d): Builder => $q->whereDate('date', $d))),
                Tables\Filters\SelectFilter::make('user_id')->label('Guru')
                    ->relationship('user', 'name', fn (Builder $query): Builder => $query->where('role', 'guru'))
                    ->searchable()->preload()->multiple(),
                Tables\Filters\SelectFilter::make('status')->label('Status Kehadiran')->options(TeacherAttendance::STATUSES)->multiple(),
            ])
            ->actions([
                // ⚡ AKSI CEPAT: CHECK IN (Muncul jika belum ada absen hari ini)
                Tables\Actions\Action::make('quick_check_in')
                    ->label('Check In')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Check In')
                    ->modalDescription('Apakah Anda yakin ingin mencatat kehadiran (Check In) untuk waktu sekarang?')
                    ->visible(fn (): bool => !TeacherAttendance::where('user_id', auth()->id())->whereDate('date', today())->exists())
                    ->action(function () {
                        $now = now();
                        // ⏰ OTOMATISASI STATUS: Jika lewat jam 08:00, otomatis Terlambat
                        $batasMasuk = today()->setTime(8, 0);
                        $status = $now->gt($batasMasuk) ? 'late' : 'present';

                        TeacherAttendance::create([
                            'user_id' => auth()->id(),
                            'date' => today(),
                            'check_in_at' => $now,
                            'status' => $status,
                            'created_by' => auth()->id(),
                        ]);

                        Notification::make()->success()->title('✅ Berhasil Check In')
                            ->body("Status Anda: " . TeacherAttendance::STATUSES[$status])->send();
                    }),

                // ⚡ AKSI CEPAT: CHECK OUT (Muncul jika sudah check in tapi belum check out hari ini)
                Tables\Actions\Action::make('quick_check_out')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Check Out')
                    ->modalDescription('Apakah Anda yakin ingin mencatat waktu pulang (Check Out) sekarang?')
                    ->visible(fn (TeacherAttendance $record): bool => $record->date->isToday() && $record->check_in_at && !$record->check_out_at)
                    ->action(function (TeacherAttendance $record) {
                        $record->update(['check_out_at' => now()]);
                        Notification::make()->success()->title('✅ Berhasil Check Out')->send();
                    }),

                Tables\Actions\ViewAction::make()->iconButton()->tooltip('Lihat Detail')->modalWidth('2xl'),
                Tables\Actions\EditAction::make()->iconButton()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Hapus')->requiresConfirmation(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->striped()->paginated([10, 25, 50, 100])->defaultPaginationPageOption(10)
            ->recordClasses(fn (TeacherAttendance $record): ?string => match ($record->status) {
                'late' => 'bg-amber-50 dark:bg-amber-950/20',
                'alpha' => 'bg-red-50 dark:bg-red-950/20',
                'present' => 'bg-green-50 dark:bg-green-950/10',
                default => null,
            })
            ->emptyStateHeading('Belum Ada Data Absensi')
            ->emptyStateDescription('Data absensi ustad akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherAttendances::route('/'),
            'create' => Pages\CreateTeacherAttendance::route('/create'),
            'view' => Pages\ViewTeacherAttendance::route('/{record}'),
            'edit' => Pages\EditTeacherAttendance::route('/{record}/edit'),
        ];
    }
}