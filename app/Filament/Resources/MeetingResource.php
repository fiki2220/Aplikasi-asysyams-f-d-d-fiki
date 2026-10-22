<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingResource\Pages;
use App\Filament\Resources\MeetingResource\RelationManagers;
use App\Features\Meetings\Models\Meeting;
use App\Features\Academic\Models\ClassGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MeetingResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Jadwal & Absensi';
    protected static ?string $modelLabel = 'Jadwal & Absensi';
    protected static ?string $pluralModelLabel = 'Jadwal & Absensi';

    protected static bool $shouldRegisterNavigation = false;

    protected static function permission(): string
    {
        return 'meetings.manage';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pertemuan')
                    ->description('Pilih kelas dan tentukan jadwal pertemuan.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\Select::make('class_group_id')
                            ->relationship('classGroup', 'name')
                            ->label('Pilih Kelas')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state) {
                                    $set('students_attendance', []);
                                    return;
                                }

                                $classGroup = ClassGroup::find($state);

                                if (! $classGroup || ! $classGroup->students) {
                                    $set('students_attendance', []);
                                    return;
                                }

                                $attendanceData = [];

                                foreach ($classGroup->students as $student) {
                                    $attendanceData[] = [
                                        'user_id' => $student->id,
                                        'name' => $student->name,
                                        'status' => 'present',
                                    ];
                                }

                                $set('students_attendance', $attendanceData);
                            }),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Pertemuan')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->maxDate(now()->addYear()),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pertemuan (Opsional)')
                            ->placeholder('Contoh: Kelas berjalan lancar, atau diganti hari lain.')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                        
                        Forms\Components\Hidden::make('title')
                            ->default('Pertemuan Rutin'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Daftar Kehadiran Santri')
                    ->description('Tentukan status kehadiran setiap santri.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Repeater::make('students_attendance')
                            ->hiddenLabel()
                            ->schema([
                                Forms\Components\Hidden::make('user_id'),

                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Santri')
                                    ->disabled()
                                    ->columnSpan(2),

                                Forms\Components\ToggleButtons::make('status')
                                    ->label('Status Kehadiran')
                                    ->options([
                                        'present'    => 'Hadir',
                                        'sick'       => 'Sakit',
                                        'permission' => 'Izin',
                                        'alpha'      => 'Alpha',
                                    ])
                                    ->colors([
                                        'present'    => 'success',
                                        'sick'       => 'info',
                                        'permission' => 'warning',
                                        'alpha'      => 'danger',
                                    ])
                                    ->inline()
                                    ->required()
                                    ->columnSpan(2),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->defaultItems(0),
                    ])
                    ->visible(fn (Forms\Get $get): bool => filled($get('class_group_id'))),

                Forms\Components\Section::make('Belum Ada Kelas Terpilih')
                    ->description('Silakan pilih kelas terlebih dahulu untuk menampilkan daftar kehadiran santri.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([])
                    ->visible(fn (Forms\Get $get): bool => ! filled($get('class_group_id'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal Pertemuan')
                    ->date('d M Y')
                    ->description(fn (Meeting $record): string => 
                        $record->date ? $record->date->translatedFormat('l') : '') 
                    ->icon('heroicon-m-calendar')
                    ->iconColor('primary')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('attendances_count')
                    ->counts('attendances')
                    ->label('Hadir')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-users'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan Kelas')
                    ->searchable()
                    ->placeholder('Tidak ada catatan')
                    ->wrap()
                    ->limit(40)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Pengajar')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->groups([
                Group::make('classGroup.name')
                    ->label('Kelas')
                    ->collapsible(), 
            ])
            ->defaultGroup('classGroup.name')
            
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['until'], fn (Builder $q, $d) => $q->whereDate('date', '<=', $d));
                    })
                    ->label('Rentang Tanggal'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit Jadwal & Absensi')
                        ->color('warning'),
                    
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus Pertemuan')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pertemuan?')
                        ->modalDescription("Data absensi pada pertemuan ini akan dihapus permanen."),
                ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->tooltip('Aksi'),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('date', 'desc')
            ->striped()->paginated([10, 25, 50])->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Pertemuan')
            ->emptyStateDescription('Buat pertemuan pertama dengan klik tombol di atas.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->role === 'guru') {
            return $query->whereHas('classGroup', fn ($q) => $q->where('teacher_id', $user->id));
        }

        return $query;
    }

    // ==========================================================
    // AUTHORIZATION (KHUSUS SUPERADMIN)
    // ==========================================================

    public static function canCreate(): bool
    {
        // Hanya Superadmin yang bisa membuat jadwal/absensi manual
        return auth()->user()->role === 'superadmin';
    }

    public static function canView($record): bool
    {
        if (! static::canAccessAction('view')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') {
            return (int) $record->classGroup?->teacher_id === (int) $user->id;
        }
        return true;
    }

    public static function canEdit($record): bool
    {
        // Hanya Superadmin yang bisa mengedit absensi secara manual
        return auth()->user()->role === 'superadmin';
    }

    public static function canDelete($record): bool
    {
        // Hanya Superadmin yang bisa menghapus absensi
        return auth()->user()->role === 'superadmin';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetings::route('/'),
            'create' => Pages\CreateMeeting::route('/create'),
            'edit' => Pages\EditMeeting::route('/{record}/edit'),
        ];
    }
}