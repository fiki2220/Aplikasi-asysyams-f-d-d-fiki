<?php

namespace App\Filament\Resources;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Grades\Models\CharacterAssessment;
use App\Filament\Resources\CharacterAssessmentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CharacterAssessmentResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = CharacterAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Aspek Karakter';
    protected static ?string $modelLabel = 'Aspek Karakter';
    protected static ?string $pluralModelLabel = 'Aspek Karakter';
    protected static ?int $navigationSort = 6;

    protected static function permission(): string
    {
        return 'grades.manage';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penilaian')
                    ->description('Pilih kelas dan santri yang akan dinilai.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Select::make('class_group_id')
                            ->label('Kelas')
                            ->options(ClassGroup::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $classGroup = ClassGroup::find($state);
                                    $set('semester_id', $classGroup?->semester_id);
                                }
                            }),

                        Forms\Components\Select::make('user_id')
                            ->label('Nama Santri')
                            ->options(function (Forms\Get $get): array {
                                $classGroupId = $get('class_group_id');
                                if (! $classGroupId) {
                                    return [];
                                }

                                return ClassGroup::find($classGroupId)
                                    ?->students()
                                    ->pluck('users.name', 'users.id')
                                    ->toArray() ?? [];
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Pilih kelas terlebih dahulu.'),

                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Otomatis terisi dari kelas yang dipilih.'),

                        Forms\Components\Hidden::make('assessor_id')
                            ->default(fn () => Auth::id()),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Penilaian Aspek Karakter')
                    ->description('Beri nilai 0-100 untuk setiap aspek. Bintang dihitung otomatis (≥90=★★★★★, ≥80=★★★★, ≥70=★★★, ≥60=★★, <60=★).')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\TextInput::make('adab_islam')
                            ->label('Adab Islam')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(80)
                            ->required()
                            ->suffix('/ 100'),

                        Forms\Components\TextInput::make('kerjasama')
                            ->label('Kerjasama')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(80)
                            ->required()
                            ->suffix('/ 100'),

                        Forms\Components\TextInput::make('tanggung_jawab')
                            ->label('Tanggung Jawab')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(80)
                            ->required()
                            ->suffix('/ 100'),

                        Forms\Components\TextInput::make('kedisiplinan')
                            ->label('Kedisiplinan')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(80)
                            ->required()
                            ->suffix('/ 100'),

                        Forms\Components\TextInput::make('kejujuran')
                            ->label('Kejujuran')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(80)
                            ->required()
                            ->suffix('/ 100')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan perkembangan karakter santri (opsional).')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('classGroup.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Semester')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('adab_islam')
                    ->label('Adab')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('kerjasama')
                    ->label('Kerja')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('tanggung_jawab')
                    ->label('T.Jwb')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('kedisiplinan')
                    ->label('Disiplin')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('kejujuran')
                    ->label('Jujur')
                    ->alignCenter()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 80 => 'info',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('average_score')
                    ->label('Rata-rata')
                    ->getStateUsing(fn (CharacterAssessment $record): string =>
                        number_format($record->average_score, 1))
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->icon('heroicon-m-star'),

                Tables\Columns\TextColumn::make('average_stars')
                    ->label('Bintang')
                    ->getStateUsing(fn (CharacterAssessment $record): string =>
                        str_repeat('★', $record->average_stars) .
                        str_repeat('☆', 5 - $record->average_stars))
                    ->alignCenter()
                    ->color('warning')
                    ->size('lg'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_group_id')
                    ->label('Kelas')
                    ->relationship('classGroup', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('below_80')
                    ->label('⚠️ Ada Nilai Di Bawah 80')
                    ->query(fn (Builder $query): Builder => $query
                        ->where(function ($q) {
                            $q->where('adab_islam', '<', 80)
                              ->orWhere('kerjasama', '<', 80)
                              ->orWhere('tanggung_jawab', '<', 80)
                              ->orWhere('kedisiplinan', '<', 80)
                              ->orWhere('kejujuran', '<', 80);
                        }))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Penilaian Karakter')
            ->emptyStateDescription('Mulai nilai aspek karakter santri dengan klik tombol di atas.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Penilaian')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    // ==========================================================
    // QUERY
    // ==========================================================

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
    // PAGES
    // ==========================================================

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCharacterAssessments::route('/'),
            'create' => Pages\CreateCharacterAssessment::route('/create'),
            'edit'   => Pages\EditCharacterAssessment::route('/{record}/edit'),
        ];
    }
}