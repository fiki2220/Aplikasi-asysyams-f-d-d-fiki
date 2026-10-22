<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Features\SiteSettings\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * SiteSettingResource
 * ============================================================
 * ⭐ HERO MEDIA:
 *   Saat edit `hero_bg`, form juga menampilkan:
 *     - Gambar Background (multiple → WebP)
 *     - Video Background WebM (multiple → raw, no convert)
 *   Video disimpan ke record terpisah `hero_video` (array of paths)
 *   dan DISEMBUNYIKAN dari tabel (auto-managed via HIDDEN_KEYS).
 *
 * PREVIEW FILEUPLOAD:
 *   Butuh CSP (`SecurityHeaders.php`) yang mengizinkan:
 *     • script-src  → 'blob:'
 *     • worker-src  → 'self' blob:
 *   Tanpa ini, preview tampil abu-abu meski file ada.
 * ============================================================
 */
class SiteSettingResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon   = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup  = 'Pengaturan';
    protected static ?string $navigationLabel  = 'Setting Website';
    protected static ?string $modelLabel       = 'Pengaturan Website';
    protected static ?string $pluralModelLabel = 'Pengaturan Website';

    // ══════════════════════════════════════════════════════════════
    // KONFIGURASI KEY
    // ══════════════════════════════════════════════════════════════

    protected const KEY_CATEGORIES = [
        'hero_bg'   => 'image',
        'footer_bg' => 'image',
        'hero_video' => 'video_webm',
        'spmb_deadline' => 'datetime',
        'teacher_attendance_late_after' => 'time',
        'kepala_rq_nama'    => 'text_input',
        'kepala_rq_jabatan' => 'text_input',
    ];

    protected const KEY_LABELS = [
        'hero_bg'                       => '🖼️ Background Hero (Gambar + Video)',
        'footer_bg'                     => '🖼️ Background Footer',
        'hero_video'                    => '🎬 Video Background Hero (WebM)',
        'spmb_deadline'                 => '⏰ Deadline SPMB',
        'teacher_attendance_late_after' => '⏰ Batas Absen Terlambat Ustad',
        'kepala_rq_nama'                => '👤 Nama Kepala RQ (Tanda Tangan Rapor)',
        'kepala_rq_jabatan'             => '💼 Jabatan Kepala RQ (Tanda Tangan Rapor)',
    ];

    protected const KEY_BADGE = [
        'hero_bg'                       => ['icon' => 'heroicon-o-photo',          'color' => 'success'],
        'footer_bg'                     => ['icon' => 'heroicon-o-photo',          'color' => 'info'],
        'hero_video'                    => ['icon' => 'heroicon-o-video-camera',   'color' => 'purple'],
        'spmb_deadline'                 => ['icon' => 'heroicon-o-calendar',       'color' => 'warning'],
        'teacher_attendance_late_after' => ['icon' => 'heroicon-o-clock',          'color' => 'warning'],
        'kepala_rq_nama'                => ['icon' => 'heroicon-o-user-circle',    'color' => 'info'],
        'kepala_rq_jabatan'             => ['icon' => 'heroicon-o-briefcase',      'color' => 'info'],
    ];

    /** ⭐ Key yang TIDAK muncul di dropdown & tabel (auto-managed). */
    protected const HIDDEN_KEYS = ['hero_video'];

    protected static function permission(): string
    {
        return 'settings.manage';
    }

    // ══════════════════════════════════════════════════════════════
    // HELPER
    // ══════════════════════════════════════════════════════════════

    protected static function categoryOf(?string $key): string
    {
        return static::KEY_CATEGORIES[$key] ?? 'text';
    }

    protected static function labelOf(?string $key): string
    {
        return static::KEY_LABELS[$key] ?? ($key ?? '-');
    }

    protected static function badgeOf(?string $key): array
    {
        return static::KEY_BADGE[$key] ?? ['icon' => 'heroicon-o-cog', 'color' => 'gray'];
    }

    protected static function selectableKeys(): array
    {
        return collect(static::KEY_LABELS)
            ->except(static::HIDDEN_KEYS)
            ->toArray();
    }

    // ══════════════════════════════════════════════════════════════
    // FORM
    // ══════════════════════════════════════════════════════════════
    public static function form(Form $form): Form
    {
        return $form->schema([

            // SECTION 1 — Pilih Key
            Forms\Components\Section::make('Pilih Pengaturan')
                ->description('Tentukan bagian website yang ingin diatur. Setiap key hanya bisa dibuat sekali.')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Select::make('key')
                        ->label('Bagian Website')
                        ->options(static::selectableKeys())
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->disabled(fn (string $operation) => $operation === 'edit')
                        ->dehydrated()
                        ->helperText(fn (string $operation) => $operation === 'edit'
                            ? '🔒 Key tidak dapat diubah setelah dibuat.'
                            : null
                        )
                        ->live()
                        ->columnSpanFull(),
                ]),

            // SECTION 2 — Media Hero (khusus key = hero_bg)
            Forms\Components\Section::make('🎨 Media Background Hero')
                ->description('Upload gambar dan video. Kalau ada video, hero akan memutar video bergantian dengan gambar.')
                ->icon('heroicon-o-photo')
                ->visible(fn (Forms\Get $get) => $get('key') === 'hero_bg')
                ->schema([

                    // 2a. Gambar (multiple)
                    Forms\Components\FileUpload::make('value_images')
                        ->label('🖼️ Gambar Background (WebP)')
                        ->helperText('Bisa upload beberapa gambar. Otomatis dikonversi ke WebP. Maks 4 MB per gambar.')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('settings')
                        ->visibility('public')
                        ->fetchFileInformation(false)
                        ->imageEditor()
                        ->imagePreviewHeight('140')
                        ->panelLayout('grid')
                        ->maxFiles(10)
                        ->maxSize(4096)
                        ->acceptedFileTypes([
                            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp',
                        ])
                        ->saveUploadedFileUsing(function ($file): string {
                            return static::convertImageToWebp($file, 'settings');
                        })
                        ->columnSpanFull(),

                    // 2b. Video WebM (multiple)
                    Forms\Components\FileUpload::make('value_video_bg')
                        ->label('🎬 Video Background (WebM) — multiple, opsional')
                        ->helperText('Bisa upload BEBERAPA video WebM sekaligus. Setiap video ditampilkan bergantian di hero slider. Rekomendasi: 8-10 detik, 1280×720, maks 5 MB per video.')
                        ->acceptedFileTypes(['video/webm'])
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(10)
                        ->maxSize(5120)
                        ->disk('public')
                        ->directory('videos')
                        ->visibility('public')
                        ->preserveFilenames(false)
                        ->fetchFileInformation(false)
                        ->panelLayout('grid')
                        ->columnSpanFull(),
                ]),

            // SECTION 3 — Isi Nilai Lain (non-hero)
            Forms\Components\Section::make('Isi Pengaturan')
                ->description('Isi nilai sesuai tipe yang muncul di bawah.')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn (Forms\Get $get) => ! empty($get('key')) && $get('key') !== 'hero_bg')
                ->schema([
                    Forms\Components\FileUpload::make('value_images')
                        ->label('Upload Gambar')
                        ->helperText('Otomatis dikonversi ke WebP.')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('settings')
                        ->visibility('public')
                        ->fetchFileInformation(false)
                        ->imageEditor()
                        ->imagePreviewHeight('180')
                        ->panelLayout('grid')
                        ->maxFiles(10)
                        ->maxSize(4096)
                        ->acceptedFileTypes([
                            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp',
                        ])
                        ->saveUploadedFileUsing(function ($file): string {
                            return static::convertImageToWebp($file, 'settings');
                        })
                        ->visible(fn (Forms\Get $get) => static::categoryOf($get('key')) === 'image')
                        ->columnSpanFull(),

                    Forms\Components\DateTimePicker::make('value_deadline')
                        ->label('Waktu Deadline')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('d F Y, H:i')
                        ->timezone('Asia/Jakarta')
                        ->helperText('Format: tanggal lengkap + jam. Contoh: 30 Juni 2026, 23:59')
                        ->visible(fn (Forms\Get $get) => static::categoryOf($get('key')) === 'datetime')
                        ->columnSpanFull(),

                    Forms\Components\TimePicker::make('value_time')
                        ->label('Batas Waktu')
                        ->seconds(false)
                        ->native(false)
                        ->displayFormat('H:i')
                        ->timezone('Asia/Jakarta')
                        ->helperText('Format: jam:menit (24 jam). Contoh: 16:40')
                        ->visible(fn (Forms\Get $get) => static::categoryOf($get('key')) === 'time')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('value_text_short')
                        ->label('Isi Value')
                        ->required()
                        ->maxLength(150)
                        ->helperText(function (Forms\Get $get): string {
                            return match ($get('key')) {
                                'kepala_rq_nama'    => '💡 Nama ini akan muncul di tanda tangan rapor PDF.',
                                'kepala_rq_jabatan' => '💡 Jabatan ini akan muncul di atas nama kepala pada rapor PDF.',
                                default             => 'Teks pendek (maks 150 karakter).',
                            };
                        })
                        ->visible(fn (Forms\Get $get) => static::categoryOf($get('key')) === 'text_input')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('value_text')
                        ->label('Isi Value')
                        ->rows(3)
                        ->visible(fn (Forms\Get $get) => static::categoryOf($get('key')) === 'text')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // TABLE
    // ══════════════════════════════════════════════════════════════
    public static function table(Table $table): Table
    {
        return $table
            // ⭐ Filter: sembunyikan key yang auto-managed (hero_video)
            ->modifyQueryUsing(fn ($query) => $query->whereNotIn('key', static::HIDDEN_KEYS))
            ->columns([

                // ── 1. Kolom Key (label + badge) ────────────────
                Tables\Columns\TextColumn::make('key')
                    ->label('Pengaturan')
                    ->formatStateUsing(fn (?string $state): string => static::labelOf($state))
                    ->description(fn ($record): string => $record?->key ?? '')
                    ->badge()
                    ->icon(fn (?string $state): string => static::badgeOf($state)['icon'])
                    ->color(fn (?string $state): string => static::badgeOf($state)['color'])
                    ->searchable()
                    ->sortable()
                    ->width('240px'),

                // ── 2. Preview Gambar (stacked) ─────────────────
                Tables\Columns\ImageColumn::make('preview_gambar')
                    ->label('Preview')
                    ->state(function ($record) {
                        if (! $record || static::categoryOf($record->key) !== 'image') {
                            return null;
                        }

                        $value = $record->value;

                        if (is_array($value)) {
                            return array_values(array_filter($value));
                        }

                        if (is_string($value) && $value !== '') {
                            $decoded = json_decode($value, true);
                            if (is_array($decoded)) {
                                return array_values(array_filter($decoded));
                            }
                            return [$value];
                        }

                        return null;
                    })
                    ->disk('public')
                    ->height(56)
                    ->width(56)
                    ->stacked()
                    ->limit(4)
                    ->limitedRemainingText()
                    ->circular(false)
                    ->extraImgAttributes([
                        'class' => 'rounded-lg border-2 border-gray-200 shadow-sm',
                    ]),

                // ── 3. Nilai — gabungan waktu / teks / jumlah file ─
                Tables\Columns\TextColumn::make('preview_value')
                    ->label('Nilai')
                    ->state(function ($record) {
                        if (! $record) {
                            return null;
                        }

                        $cat   = static::categoryOf($record->key);
                        $value = $record->value;

                        // Gambar → tampil jumlah
                        if ($cat === 'image') {
                            $count = is_array($value) ? count($value) : 0;
                            return $count > 0 ? "{$count} gambar" : null;
                        }

                        // Video → tampil jumlah
                        if ($cat === 'video_webm') {
                            $count = is_array($value)
                                ? count(array_filter($value))
                                : (int) ! empty($value);
                            return $count > 0 ? "🎬 {$count} video" : null;
                        }

                        // Datetime → format panjang
                        if ($cat === 'datetime' && $value) {
                            try {
                                return \Carbon\Carbon::parse($value)
                                    ->translatedFormat('d M Y, H:i');
                            } catch (\Throwable $e) {
                                return $value;
                            }
                        }

                        // Time → format H:i
                        if ($cat === 'time' && $value) {
                            return $value;
                        }

                        // Text / Text Input
                        return $value;
                    })
                    ->icon(function ($record) {
                        if (! $record) {
                            return 'heroicon-o-document-text';
                        }
                        return match (static::categoryOf($record->key)) {
                            'image'      => 'heroicon-o-photo',
                            'video_webm' => 'heroicon-o-video-camera',
                            'datetime'   => 'heroicon-o-calendar',
                            'time'       => 'heroicon-o-clock',
                            'text_input' => 'heroicon-o-pencil-square',
                            default      => 'heroicon-o-document-text',
                        };
                    })
                    ->iconColor(function ($record) {
                        if (! $record) {
                            return 'gray';
                        }
                        return match (static::categoryOf($record->key)) {
                            'image'      => 'success',
                            'video_webm' => 'purple',
                            'datetime'   => 'warning',
                            'time'       => 'info',
                            'text_input' => 'info',
                            default      => 'gray',
                        };
                    })
                    ->badge(fn ($record) => $record && in_array(static::categoryOf($record->key), ['datetime', 'time']))
                    ->color(function ($record) {
                        if (! $record) {
                            return 'gray';
                        }
                        return match (static::categoryOf($record->key)) {
                            'datetime' => 'warning',
                            'time'     => 'info',
                            default    => 'gray',
                        };
                    })
                    ->limit(50)
                    ->tooltip(fn ($record) => $record && is_string($record->value) ? $record->value : null)
                    ->placeholder('—'),

                // ── 4. Update Terakhir ──────────────────────────
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->since()
                    ->dateTimeTooltip('d F Y, H:i')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('Ubah'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('Hapus'),
            ])
            ->bulkActions([])
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum ada pengaturan')
            ->emptyStateDescription('Klik "Buat" untuk menambahkan pengaturan pertama.')
            ->emptyStateIcon('heroicon-o-cog-6-tooth');
    }

    // ══════════════════════════════════════════════════════════════
    // MUTATIONS
    // ══════════════════════════════════════════════════════════════
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return static::mergeVirtualFieldsToValue($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return static::mergeVirtualFieldsToValue($data);
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        $key   = $data['key']   ?? null;
        $value = $data['value'] ?? null;
        $cat   = static::categoryOf($key);

        switch ($cat) {
            case 'image':
                $data['value_images'] = static::normalizeImageValue($value);
                break;
            case 'datetime':
                $data['value_deadline'] = $value;
                break;
            case 'time':
                $data['value_time'] = static::extractTimePart($value);
                break;
            case 'text_input':
                $data['value_text_short'] = is_string($value) ? $value : null;
                break;
            case 'text':
            default:
                $data['value_text'] = is_string($value) ? $value : null;
                break;
        }

        // ⭐ Kalau hero_bg, muat SEMUA video WebM dari record hero_video
        if ($key === 'hero_bg') {
            $heroVideo = SiteSetting::where('key', 'hero_video')->first();
            if ($heroVideo) {
                $val = $heroVideo->value;
                if (is_array($val)) {
                    $data['value_video_bg'] = array_values(array_filter($val));
                } elseif (is_string($val) && $val !== '') {
                    $decoded = json_decode($val, true);
                    $data['value_video_bg'] = is_array($decoded) ? $decoded : [$val];
                } else {
                    $data['value_video_bg'] = [];
                }
            } else {
                $data['value_video_bg'] = [];
            }
        }

        return $data;
    }

    public static function mergeVirtualFieldsToValue(array $data): array
    {
        $key = $data['key'] ?? null;
        $cat = static::categoryOf($key);

        if (empty($key)) {
            unset(
                $data['value_images'], $data['value_video_bg'],
                $data['value_deadline'], $data['value_time'],
                $data['value_text_short'], $data['value_text']
            );
            return $data;
        }

        $images    = $data['value_images']     ?? null;
        $deadline  = $data['value_deadline']   ?? null;
        $time      = $data['value_time']       ?? null;
        $textShort = $data['value_text_short'] ?? null;
        $text      = $data['value_text']       ?? null;

        switch ($cat) {
            case 'image':
                $data['value'] = is_array($images)
                    ? array_values(array_filter($images))
                    : ($images ? [$images] : []);
                break;
            case 'datetime':
                $data['value'] = $deadline;
                break;
            case 'time':
                $data['value'] = $time;
                break;
            case 'text_input':
                $data['value'] = $textShort;
                break;
            case 'text':
            default:
                $data['value'] = $text;
                break;
        }

        unset(
            $data['value_images'],
            $data['value_video_bg'],   // ⭐ virtual — disimpan ke record hero_video (di Page)
            $data['value_deadline'],
            $data['value_time'],
            $data['value_text_short'],
            $data['value_text']
        );

        return $data;
    }

    // ══════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════
    protected static function normalizeImageValue($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
            return [$value];
        }
        return [];
    }

    protected static function extractTimePart($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (is_string($value) && preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value;
        }
        try {
            return \Carbon\Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ══════════════════════════════════════════════════════════════
    // ⭐ HELPER — Konversi gambar → WebP
    // ══════════════════════════════════════════════════════════════
    protected static function convertImageToWebp($file, string $directory = 'settings'): string
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        $manager = new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );

        $image = $manager->read($file->getRealPath());
        $image->scaleDown(width: 2000, height: 2000);
        $encoded = (string) $image->toWebp(85);

        $filename = trim($directory, '/') . '/' . \Illuminate\Support\Str::uuid() . '.webp';
        $disk->put($filename, $encoded);

        return $filename;
    }

    // ══════════════════════════════════════════════════════════════
    // PAGES
    // ══════════════════════════════════════════════════════════════
    public static function getPages(): array
    {
        return [
            'index'                     => Pages\ListSiteSettings::route('/'),
            'create'                    => Pages\CreateSiteSetting::route('/create'),
            'edit'                      => Pages\EditSiteSetting::route('/{record}/edit'),
            'manage-spmb'               => Pages\ManageSPMBDeadline::route('/spmb-deadline'),
            'manage-teacher-attendance' => Pages\ManageTeacherAttendanceSchedule::route('/teacher-attendance-schedule'),
        ];
    }
}