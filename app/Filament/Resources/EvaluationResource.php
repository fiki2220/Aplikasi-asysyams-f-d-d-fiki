<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Features\Academic\Models\ClassGroup;
use App\Features\Grades\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EvaluationResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Input Evaluasi';
    protected static ?string $modelLabel = 'Evaluasi';
    protected static ?string $pluralModelLabel = 'Input Evaluasi';
    protected static ?int $navigationSort = 3;

    protected static function permission(): string
    {
        return 'evaluations.manage';
    }

    public const JENIS_OPTIONS = [
        'tilawah'  => 'Tilawah',
        'murottal' => 'Murottal',
    ];

    public const JENIS_COLORS = [
        'tilawah'  => 'success',
        'murottal' => 'info',
    ];

    public const JENIS_ICONS = [
        'tilawah'  => 'heroicon-m-book-open',
        'murottal' => 'heroicon-m-musical-note',
    ];

    public const MAQRA_WAJIB_OPTIONS = [
        "Ali 'Imran : 144" => "QS Ali 'Imran 144",
        "Qaf : 16"          => "QS Qaf 16",
        "At-Taubah : 81"    => "QS At-Taubah 81",
        "Al-Ma'idah : 27"   => "QS Al-Ma'idah 27",
        "Al-Isra : 1"       => "QS Al-Isra 1",
        "Al-A'raf : 31"     => "QS Al-A'raf 31",
        "Ar-Rahman : 1"     => "QS Ar-Rahman 1",
    ];

    public const MAQOM_OPTIONS = [
        'Bayyati'  => 'Maqom Bayyati',
        'Hijaz'    => 'Maqom Hijaz',
        'Nahwand'  => 'Maqom Nahwand',
        'Rast'     => 'Maqom Rast',
        'Sikah'    => 'Maqom Sikah',
        'Shoba'    => 'Maqom Shoba',
        'Jiharkah' => 'Maqom Jiharkah',
    ];

    public static function maqomLabel(?string $kode): string
    {
        if (! $kode) return '—';
        if (isset(self::MAQOM_OPTIONS[$kode])) return self::MAQOM_OPTIONS[$kode];
        if (str_contains($kode, 'Bayyati')) return 'Maqom Bayyati';
        if (str_contains($kode, 'Hijaz')) return 'Maqom Hijaz';
        if (str_contains($kode, 'Nahwand')) return 'Maqom Nahwand';
        if (str_contains($kode, 'Rast')) return 'Maqom Rast';
        if (str_contains($kode, 'Sikah')) return 'Maqom Sikah';
        if (str_contains($kode, 'Shoba')) return 'Maqom Shoba';
        if (str_contains($kode, 'Jiharkah')) return 'Maqom Jiharkah';
        return $kode;
    }

    // Fungsi bantu ekstrak JSON ke angka
    public static function extractNilaiFromItems($items): ?float
    {
        if (is_string($items)) $items = json_decode($items, true) ?? [];
        if (! is_array($items) || empty($items)) return null;
        
        $scores = array_filter(array_map(fn($i) => $i['score'] ?? null, $items), fn($s) => $s !== null && $s !== '');
        if (empty($scores)) return null;
        
        return round(array_sum($scores) / count($scores), 2);
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Evaluasi')
                    ->description('Pilih kelas, jenis evaluasi, dan santri.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Select::make('class_group_id')
                            ->label('Kelas')
                            ->options(ClassGroup::query()->pluck('name', 'id'))
                            ->required()->searchable()->preload()->native(false)->live(),

                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Evaluasi')
                            ->options(self::JENIS_OPTIONS)
                            ->default('tilawah')->required()->native(false)->live(),

                        Forms\Components\Select::make('user_ids')
                            ->label('Pilih Santri (bisa pilih banyak)')
                            ->options(function (Forms\Get $get): array {
                                $classGroupId = $get('class_group_id');
                                if (! $classGroupId) return [];
                                return ClassGroup::find($classGroupId)
                                    ?->students()->pluck('users.name', 'users.id')->toArray() ?? [];
                            })
                            ->multiple()->required()->searchable()->preload()->native(false)
                            ->visible(fn(string $operation): bool => $operation === 'create'),

                        Forms\Components\Select::make('user_id')
                            ->label('Santri')
                            ->options(function (Forms\Get $get): array {
                                $classGroupId = $get('class_group_id');
                                if (! $classGroupId) return [];
                                return ClassGroup::find($classGroupId)
                                    ?->students()->pluck('users.name', 'users.id')->toArray() ?? [];
                            })
                            ->required()->searchable()->preload()->native(false)
                            ->visible(fn(string $operation): bool => $operation === 'edit'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Evaluasi')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\Select::make('surah_name')
                            ->label('Maqra\' Wajib')
                            ->options(self::MAQRA_WAJIB_OPTIONS)
                            ->placeholder('— Pilih Maqra\' Wajib —')
                            ->searchable()->required()->native(false)
                            ->visible(fn(Forms\Get $get): bool => $get('jenis') === 'tilawah')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('surah_name')
                            ->label('Surah / Ayat')
                            ->placeholder('Contoh: Al-Baqarah 1-5')
                            ->required()->maxLength(150)
                            ->visible(fn(Forms\Get $get): bool => $get('jenis') === 'murottal')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('song_name')
                            ->label('Maqom / Lagu')
                            ->options(self::MAQOM_OPTIONS)
                            ->placeholder('— Pilih Maqom —')
                            ->searchable()->native(false)->required()
                            ->columnSpanFull(),

                        // ⭐ Nilai disiapkan untuk ditangkap oleh Page
                        Forms\Components\TextInput::make('nilai_evaluasi')
                            ->label('Nilai Evaluasi')
                            ->numeric()->minValue(0)->maxValue(100)->step(0.01)
                            ->suffix('/ 100')->placeholder('Contoh: 90')
                            ->required()
                            ->dehydrated() 
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, ?Evaluation $record) {
                                if ($record) {
                                    $component->state(static::extractNilaiFromItems($record->items));
                                }
                            })
                            ->columnSpanFull()
                            ->helperText('Masukkan nilai total (0-100).'),
                    ])->columns(1),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jenis')->label('Jenis')->badge()
                    ->formatStateUsing(fn(?string $state): string => self::JENIS_OPTIONS[$state] ?? ucfirst($state ?? '—'))
                    ->color(fn(?string $state): string => self::JENIS_COLORS[$state] ?? 'gray')
                    ->icon(fn(?string $state): string => self::JENIS_ICONS[$state] ?? 'heroicon-m-question-mark-circle'),

                Tables\Columns\TextColumn::make('surah_name')->label('Surah / Maqra\'')
                    ->searchable()->limit(30)->placeholder('—')->icon('heroicon-m-book-open'),

                Tables\Columns\TextColumn::make('song_name')->label('Maqom / Lagu')
                    ->searchable()->formatStateUsing(fn(?string $state): string => self::maqomLabel($state))
                    ->limit(35)->placeholder('—')->icon('heroicon-m-musical-note'),

                Tables\Columns\TextColumn::make('nilai_evaluasi')
                    ->label('Nilai')->badge()->color('warning')->icon('heroicon-m-star')->alignCenter()
                    ->state(fn(Evaluation $record): ?float => static::extractNilaiFromItems($record->items))
                    ->formatStateUsing(fn($state): string => $state !== null ? number_format((float) $state, 2) : '—'),

                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')
                    ->description(fn(Evaluation $record): string => $record->created_at?->diffForHumans() ?? '')
                    ->sortable(),
            ])
            ->defaultGroup('student.name')
            ->groups([
                Group::make('student.name')->label('Santri')->collapsible()
                    ->getTitleFromRecordUsing(fn(Evaluation $record): string => ($record->student?->name ?? '—') . ' — ' . ($record->classGroup?->name ?? '—'))
                    ->getDescriptionFromRecordUsing(fn(Evaluation $record): string => 'NISN: ' . ($record->student?->nisn ?? '—')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')->label('Jenis Evaluasi')->options(self::JENIS_OPTIONS),
                Tables\Filters\SelectFilter::make('class_group_id')->label('Kelas')->relationship('classGroup', 'name')->searchable()->preload()->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip('Lihat Detail')->modalWidth('4xl'),
                Tables\Actions\EditAction::make()->iconButton()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Hapus')->requiresConfirmation(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('user_id', 'asc')->striped()
            ->paginated([25, 50, 100])->defaultPaginationPageOption(25);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && $user->role === 'guru') {
            return $query->whereHas('classGroup', fn($q) => $q->where('teacher_id', $user->id));
        }
        return $query;
    }

    public static function canView($record): bool
    {
        if (! static::canAccessAction('view')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->classGroup?->teacher_id === (int) $user->id;
        return true;
    }

    public static function canEdit($record): bool
    {
        if (! static::canAccessAction('update')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->classGroup?->teacher_id === (int) $user->id;
        return true;
    }

    public static function canDelete($record): bool
    {
        if (! static::canAccessAction('delete')) return false;
        $user = auth()->user();
        if ($user && $user->role === 'guru') return (int) $record->classGroup?->teacher_id === (int) $user->id;
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view'   => Pages\ViewEvaluation::route('/{record}'),
            'edit'   => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}