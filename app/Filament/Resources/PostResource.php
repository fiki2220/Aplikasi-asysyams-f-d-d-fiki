<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Features\Posts\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Publikasi';
    protected static ?string $navigationLabel = 'Berita & Artikel';
    protected static ?string $modelLabel = 'Berita & Artikel';
    protected static ?string $pluralModelLabel = 'Berita & Artikel';

    protected static function permission(): string
    {
        return 'posts.manage';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: KONTEN UTAMA
                Forms\Components\Section::make('Konten Utama')
                    ->description('Judul, kategori, dan isi berita.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Berita')
                            ->placeholder('Contoh: SPMB Asy-Syams Tahun Ajaran 2026/2027 Resmi Dibuka')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL otomatis. Bisa diedit manual jika perlu.')
                            ->prefix(fn () => url('/berita') . '/')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'Pendidikan' => 'Pendidikan',
                                'Prestasi' => 'Prestasi',
                                'Dakwah' => 'Dakwah',
                                'Sosial' => 'Sosial',
                                'Umum' => 'Umum',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\DatePicker::make('published_at')
                            ->label('Tanggal Terbit')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->required(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Berita')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike', 'link',
                                'h2', 'h3', 'bulletList', 'orderedList',
                                'blockquote', 'redo', 'undo',
                            ]),
                    ])
                    ->columns(2),

                // SECTION 2: GAMBAR
                Forms\Components\Section::make('Foto Utama')
                    ->description('Upload foto utama berita. Format: JPG/PNG, maks 3MB.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Utama')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                            ->directory('posts')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(3072)
                            ->helperText('Resolusi direkomendasikan: 1200×675 px (16:9)')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('image_caption')
                            ->label('Keterangan Foto (Caption)')
                            ->placeholder('Contoh: Santri sedang mengikuti kegiatan panen raya')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // SECTION 3: PUBLIKASI
                Forms\Components\Section::make('Status Publikasi')
                    ->description('Atur apakah berita langsung terbit atau disimpan sebagai draft.')
                    ->icon('heroicon-o-rocket-launch')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Terbitkan Sekarang?')
                            ->helperText('Jika dimatikan, berita akan disimpan sebagai draft.')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(1),

                // Hidden field: penulis otomatis
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Thumbnail foto (lebih kecil)
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->height(50)
                    ->width(80)
                    ->defaultImageUrl(fn (Post $record) => 'https://ui-avatars.com/api/?name=' . urlencode(Str::limit($record->title, 2, '')) . '&background=166534&color=fff&size=80')
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                // Judul + excerpt
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->limit(60)
                    ->description(fn (Post $record): ?string => $record->category
                        ? $record->category . ' • ' . ($record->author?->name ?? 'Tanpa Penulis')
                        : null),

                // Kategori badge
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Pendidikan' => 'info',
                        'Prestasi'   => 'warning',
                        'Dakwah'     => 'success',
                        'Sosial'     => 'primary',
                        'Umum'       => 'gray',
                        default      => 'gray',
                    })
                    ->toggleable(),

                // Penulis
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Penulis')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Views
                Tables\Columns\TextColumn::make('views')
                    ->label('Dilihat')
                    ->icon('heroicon-m-eye')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                // Status publikasi
                Tables\Columns\TextColumn::make('is_published')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Terbit' : 'Draft')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->icon(fn (bool $state): string => $state
                        ? 'heroicon-m-check-circle'
                        : 'heroicon-m-pencil')
                    ->sortable(),

                // Tanggal terbit + relative
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Terbit')
                    ->date('d M Y')
                    ->description(fn (Post $record): string => $record->published_at?->diffForHumans() ?? '')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'Pendidikan' => 'Pendidikan',
                        'Prestasi' => 'Prestasi',
                        'Dakwah' => 'Dakwah',
                        'Sosial' => 'Sosial',
                        'Umum' => 'Umum',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('✅ Terbit')
                    ->falseLabel('📝 Draft'),

                Tables\Filters\Filter::make('published_at')
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
                            ->when($data['from'], fn ($q, $d) => $q->whereDate('published_at', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('published_at', '<=', $d));
                    })
                    ->label('Rentang Tanggal Terbit'),
            ])
            ->actions([
                // Lihat di web
                Tables\Actions\Action::make('view_web')
                    ->label('Lihat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->tooltip('Buka di Website')
                    ->color('gray')
                    ->url(fn (Post $record): string => route('post.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record): bool => (bool) $record->is_published),

                // Toggle publish cepat
                Tables\Actions\Action::make('toggle_publish')
                    ->label(fn (Post $record): string => $record->is_published ? 'Jadikan Draft' : 'Terbitkan')
                    ->icon(fn (Post $record): string => $record->is_published
                        ? 'heroicon-o-arrow-uturn-left'
                        : 'heroicon-o-rocket-launch')
                    ->iconButton()
                    ->tooltip(fn (Post $record): string => $record->is_published ? 'Jadikan Draft' : 'Terbitkan')
                    ->color(fn (Post $record): string => $record->is_published ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Post $record): string => $record->is_published
                        ? 'Jadikan Draft?'
                        : 'Terbitkan Berita Ini?')
                    ->modalDescription(fn (Post $record): string => $record->is_published
                        ? 'Berita tidak akan terlihat di website sampai diterbitkan kembali.'
                        : 'Berita akan langsung muncul di website publik.')
                    ->action(function (Post $record) {
                        $record->update([
                            'is_published' => ! $record->is_published,
                            'published_at' => ! $record->is_published ? now() : $record->published_at,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title($record->is_published ? '✅ Berita Diterbitkan' : '📝 Berita Dijadikan Draft')
                            ->body("\"{$record->title}\"")
                            ->success()
                            ->send();
                    }),

                // Duplikat
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->iconButton()
                    ->tooltip('Duplikat Berita')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Duplikat Berita?')
                    ->modalDescription('Berita baru akan dibuat sebagai draft dengan data yang sama.')
                    ->action(function (Post $record) {
                        $new = $record->replicate();
                        $new->title = $record->title . ' (Copy)';
                        $new->slug = $record->slug . '-copy-' . time();
                        $new->is_published = false;
                        $new->views = 0;
                        $new->save();

                        \Filament\Notifications\Notification::make()
                            ->title('✅ Berita Berhasil Diduplikat')
                            ->body("Berita baru: \"{$new->title}\"")
                            ->success()
                            ->send();
                    }),

                // Edit
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),

                // Hapus
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_publish')
                        ->label('Terbitkan Terpilih')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update([
                                'is_published' => true,
                                'published_at' => now(),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title("✅ {$records->count()} Berita Diterbitkan")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('bulk_draft')
                        ->label('Jadikan Draft')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_published' => false]);

                            \Filament\Notifications\Notification::make()
                                ->title("📝 {$records->count()} Berita Dijadikan Draft")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->recordClasses(fn (Post $record): ?string =>
                ! $record->is_published ? 'bg-amber-50 dark:bg-amber-950/20' : null
            )
            ->emptyStateHeading('Belum Ada Berita')
            ->emptyStateDescription('Mulai tulis berita atau artikel pertama Anda.')
            ->emptyStateIcon('heroicon-o-newspaper');
    }

    // ==========================================================
    // PAGES
    // ==========================================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}