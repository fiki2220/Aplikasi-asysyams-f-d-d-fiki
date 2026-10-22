<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassGroupResource\Pages;
use App\Features\Academic\Models\ClassGroup;
use App\Features\Academic\Models\Semester;
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

class ClassGroupResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = ClassGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Kelola Kelas';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Kelola Kelas';
    protected static ?int $navigationSort = 1;

    protected static function permission(): string
    {
        return 'classes.manage';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->description('Tentukan jenis kelas, huruf kelas, jadwal, dan semester.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Select::make('class_type')
                            ->label('Jenis Kelas')
                            ->options(ClassGroup::classTypeOptions())
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if (! ClassGroup::needsClassLetter($state)) {
                                    $set('class_letter', null);
                                }
                                static::generateClassName($set, $get);
                            }),

                        Forms\Components\Select::make('class_letter')
                            ->label('Huruf Kelas')
                            ->options(ClassGroup::classLetterOptions())
                            ->required(fn (Forms\Get $get): bool => ClassGroup::needsClassLetter($get('class_type')))
                            ->visible(fn (Forms\Get $get): bool => ClassGroup::needsClassLetter($get('class_type')))
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => static::generateClassName($set, $get)),

                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->options(Semester::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->native(false),

                        Forms\Components\TimePicker::make('start_time')
                            ->label('Jam Masuk Kelas')
                            ->seconds(false)
                            ->displayFormat('H:i')
                            ->helperText('Contoh: 16:30'),

                        Forms\Components\TimePicker::make('end_time')
                            ->label('Jam Selesai Kelas')
                            ->seconds(false)
                            ->displayFormat('H:i')
                            ->helperText('Contoh: 18:00'),

                        // ✅ INPUT HARI MASUK
                        Forms\Components\CheckboxList::make('days')
                            ->label('Hari Masuk Kelas')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                                'Minggu' => 'Minggu',
                            ])
                            ->columns(4)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Pilih hari kelas ini berlangsung.'),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kelas')
                            ->placeholder('Otomatis terisi dari jenis & huruf kelas')
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(fn (Forms\Set $set, Forms\Get $get) => static::generateClassName($set, $get))
                            ->helperText('Nama kelas di-generate otomatis.'),

                        Forms\Components\Select::make('teacher_id')
                            ->label('Ustad / Pengajar')
                            ->options(User::whereIn('role', ['superadmin', 'guru'])->pluck('name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->nullable()
                            ->placeholder('Pilih pengajar (opsional)'),

                        Forms\Components\Hidden::make('slug'),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function generateClassName(Forms\Set $set, Forms\Get $get): void
    {
        $name = ClassGroup::generateNameFromTypeAndLetter(
            $get('class_type'),
            $get('class_letter'),
        );

        if ($name === '-') {
            $set('name', null);
            return;
        }

        $set('name', $name);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Kelas')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')->label('Nama Kelas')->weight('bold')->size('lg'),
                        Infolists\Components\TextEntry::make('class_type')->label('Jenis Kelas')->badge()->formatStateUsing(fn (?string $state): string => ClassGroup::classTypeLabel($state))->color('primary'),
                        Infolists\Components\TextEntry::make('class_letter')->label('Huruf Kelas')->placeholder('—')->badge(),
                        
                        Infolists\Components\TextEntry::make('start_time')->label('Jam Masuk')->dateTime('H:i')->placeholder('—')->icon('heroicon-m-clock'),
                        Infolists\Components\TextEntry::make('end_time')->label('Jam Pulang')->dateTime('H:i')->placeholder('—')->icon('heroicon-m-clock'),

                        // ✅ TAMPILKAN HARI DI INFOLIST
                        Infolists\Components\TextEntry::make('days')
                            ->label('Hari Masuk')
                            ->badge()
                            ->color('info')
                            ->placeholder('—')
                            ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),

                        Infolists\Components\TextEntry::make('semester.name')->label('Semester')->badge()->color('success'),
                        Infolists\Components\TextEntry::make('teacher.name')->label('Ustad / Pengajar')->icon('heroicon-m-user-circle')->placeholder('Belum ditentukan'),
                        Infolists\Components\TextEntry::make('students_count')->label('Jumlah Santri')->getStateUsing(fn (ClassGroup $record): int => $record->students()->count())->badge()->color('info')->icon('heroicon-m-users'),
                        Infolists\Components\TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->since()->color('gray'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-academic-cap')
                    ->description(fn (ClassGroup $record): ?string =>
                        $record->teacher?->name ? 'Ustad: ' . $record->teacher->name : null),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jadwal Kelas')
                    ->getStateUsing(fn (ClassGroup $record): string =>
                        ($record->start_time ? $record->start_time->format('H:i') : '--:--') . ' - ' . 
                        ($record->end_time ? $record->end_time->format('H:i') : '--:--'))
                    ->icon('heroicon-m-clock')
                    ->badge()
                    ->color('gray'),

                // ✅ TAMPILKAN HARI DI TABEL
                Tables\Columns\TextColumn::make('days')
                    ->label('Hari')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),

                Tables\Columns\TextColumn::make('class_type')
                    ->label('Jenis Kelas')
                    ->formatStateUsing(fn (?string $state): string => ClassGroup::classTypeLabel($state))
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Semester')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-calendar')
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Ustad')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('—')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Santri')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state < 5 => 'warning',
                        default => 'success',
                    })
                    ->icon('heroicon-m-users')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_type')->label('Jenis Kelas')->options(ClassGroup::classTypeOptions())->multiple(),
                Tables\Filters\SelectFilter::make('semester_id')->label('Semester')->options(Semester::pluck('name', 'id'))->searchable()->multiple(),
                Tables\Filters\SelectFilter::make('teacher_id')->label('Ustad / Pengajar')->options(User::whereIn('role', ['superadmin', 'guru'])->pluck('name', 'id'))->searchable()->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip('Lihat Detail'),
                Tables\Actions\EditAction::make()->iconButton()->tooltip('Edit'),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')->icon('heroicon-o-document-duplicate')->iconButton()->tooltip('Duplikat Kelas')->color('info')
                    ->requiresConfirmation()->modalHeading('Duplikat Kelas Ini?')->modalDescription('Kelas baru akan dibuat. Silakan ubah huruf kelas setelah.')
                    ->action(function (ClassGroup $record) {
                        $new = $record->replicate();
                        $new->name = $record->name . ' (Copy)';
                        $new->slug = $record->slug . '-copy-' . time();
                        $new->save();
                        Notification::make()->title('✅ Kelas Berhasil Diduplikat')->body("Kelas baru: \"{$new->name}\"")->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Hapus')->requiresConfirmation(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc')->striped()
            ->paginated([10, 25, 50])->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Kelas')->emptyStateDescription('Buat kelas pertama Anda untuk mulai mengelola santri.')->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ClassGroupResource\RelationManagers\StudentsRelationManager::class,
            \App\Filament\Resources\ClassGroupResource\RelationManagers\MeetingsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && $user->role === 'guru') {
            return $query->where('teacher_id', $user->id);
        }
        return $query;
    }

    public static function canView($record): bool
    {
        if (! static::canAccessAction('view')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->teacher_id === (int) $user->id;
        return true;
    }

    public static function canEdit($record): bool
    {
        if (! static::canAccessAction('update')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->teacher_id === (int) $user->id;
        return true;
    }

    public static function canDelete($record): bool
    {
        if (! static::canAccessAction('delete')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->teacher_id === (int) $user->id;
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassGroups::route('/'),
            'create' => Pages\CreateClassGroup::route('/create'),
            'view' => Pages\ViewClassGroup::route('/{record}'),
            'edit' => Pages\EditClassGroup::route('/{record}/edit'),
        ];
    }
}