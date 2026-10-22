<?php

namespace App\Filament\Resources;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Grades\Models\Assessment;
use App\Filament\Resources\AssessmentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AssessmentResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Akademik (Ustad)';
    protected static ?string $navigationLabel = 'Input Penilaian Santri';
    protected static ?string $modelLabel = 'Penilaian';
    protected static ?string $pluralModelLabel = 'Input Penilaian';
    protected static ?int $navigationSort = 2;

    protected static function permission(): string
    {
        return 'assessments.manage';
    }

    // ==========================================================
    // KONSTANTA
    // ==========================================================

    public const JENIS_LABELS = [
        'ziyadah'  => 'Ziyadah',
        'murojaah' => 'Murojaah',
        'murottal' => 'Murottal',
        'tahsin'   => 'Tahsin',
        'tilawah'  => 'Tilawah',
    ];

    public const JENIS_LABELS_LONG = [
        'ziyadah'  => 'Ziyadah (Hafalan Baru)',
        'murojaah' => 'Murojaah (Mengulang)',
        'murottal' => 'Murottal (Membaca Tartil)',
        'tahsin'   => 'Tahsin (Perbaikan Bacaan)',
        'tilawah'  => 'Tilawah (Membaca)',
    ];

    public const JENIS_COLORS = [
        'ziyadah'  => 'success',
        'murojaah' => 'info',
        'murottal' => 'primary',
        'tahsin'   => 'warning',
        'tilawah'  => 'danger',
    ];

    public const JENIS_ICONS = [
        'ziyadah'  => 'heroicon-m-arrow-trending-up',
        'murojaah' => 'heroicon-m-arrow-path',
        'murottal' => 'heroicon-m-musical-note',
        'tahsin'   => 'heroicon-m-academic-cap',
        'tilawah'  => 'heroicon-m-book-open',
    ];

    public const NILAI_PENINGKATAN_MAPEL = [
        'nilai_tilawah'  => 'Tilawah',
        'nilai_murottal' => 'Murottal',
        'nilai_tahsin'   => 'Tahsin',
        'nilai_tahfizh'  => 'Tahfizh',
    ];

    public const AMBANG_L = 85;
    public const AMBANG_C = 70;

    // ═══════════════════════════════════════════════════════════
    // MAQRA' WAJIB — Preset untuk Tilawah
    // ═══════════════════════════════════════════════════════════
    public const MAQRA_WAJIB = [
        "QS Ali 'Imran 144" => ['surah' => "Ali 'Imran",  'ayat' => '144'],
        "QS Qaf 16"          => ['surah' => 'Qaf',          'ayat' => '16'],
        "QS At-Taubah 81"    => ['surah' => 'At-Taubah',    'ayat' => '81'],
        "QS Al-Ma'idah 27"   => ['surah' => "Al-Ma'idah",   'ayat' => '27'],
        "QS Al-Isra 1"       => ['surah' => 'Al-Isra',      'ayat' => '1'],
        "QS Al-A'raf 31"     => ['surah' => "Al-A'raf",     'ayat' => '31'],
        "QS Ar-Rahman 1"     => ['surah' => 'Ar-Rahman',    'ayat' => '1'], // ✅ Tambahan Baru
    ];

    // ═══════════════════════════════════════════════════════════
    // MAQOM (Lagu) — untuk Tilawah
    // ═══════════════════════════════════════════════════════════
    public const MAQOM_OPTIONS = [
        'Maqom Bayyati' => [
            'Bayyati Awal Maqom'         => 'Awal Maqom',
            'Bayyati Qoror'              => 'Qoror',
            'Bayyati Nawa'               => 'Nawa',
            'Bayyati Husaini'            => 'Husaini',
            'Bayyati Jawab'              => 'Jawab',
            'Bayyati Jawabul Jawab'      => 'Jawabul Jawab',
            'Bayyati Jawab Syuri'        => 'Jawab Syuri',
            'Bayyati Jawabul Jawab Syuri' => 'Jawabul Jawab Syuri',
            'Bayyati Penutup'            => 'Penutup',
        ],
        'Maqom Hijaz' => [
            'Hijaz Awal Maqom'    => 'Awal Maqom',
            'Hijaz Jawab'         => 'Jawab',
            'Hijaz Jawabul Jawab' => 'Jawabul Jawab',
            'Hijaz Kar/Kur'       => 'Kar / Kur',
        ],
        'Maqom Nahwand' => [
            'Nahwand Awal Maqom'    => 'Awal Maqom',
            'Nahwand Jawab'         => 'Jawab',
            'Nahwand Jawabul Jawab' => 'Jawabul Jawab',
        ],
        'Maqom Rast' => [
            'Rast Awal Maqom'    => 'Awal Maqom',
            'Rast Jawab'         => 'Jawab',
            'Rast Jawabul Jawab' => 'Jawabul Jawab',
        ],
        'Maqom Sikah' => [
            'Sikah Awal Maqom'    => 'Awal Maqom',
            'Sikah Jawab'         => 'Jawab',
            'Sikah Jawabul Jawab' => 'Jawabul Jawab',
        ],
        'Maqom Shoba' => [
            'Shoba Awal Maqom'    => 'Awal Maqom',
            'Shoba Jawab'         => 'Jawab',
            'Shoba Jawabul Jawab' => 'Jawabul Jawab',
        ],
        'Maqom Jiharkah' => [
            'Jiharkah Awal Maqom'    => 'Awal Maqom',
            'Jiharkah Jawab'         => 'Jawab',
            'Jiharkah Jawabul Jawab' => 'Jawabul Jawab',
        ],
    ];

    // ==========================================================
    // MAPEL PER KELAS
    // ==========================================================

    public static function mapelUntukKelas(?string $className): array
    {
        $lower = strtolower($className ?? '');

        if (str_contains($lower, 'murottal') || str_contains($lower, 'murotal')) {
            return ['Murottal', 'Tahsin', 'Tahfizh'];
        }

        if (str_contains($lower, 'tahsin')) {
            return ['Tahsin', 'Tahfizh'];
        }

        if (str_contains($lower, 'tilawah')) {
            return ['Tilawah', 'Tahsin', 'Tahfizh'];
        }

        return ['Tilawah', 'Tahsin', 'Tahfizh'];
    }

    public static function kelasPunyaMapel(?string $className, string $mapel): bool
    {
        return in_array($mapel, static::mapelUntukKelas($className), true);
    }

    protected static function classNameFromForm(Forms\Get $get): ?string
    {
        $id = $get('class_group_id');
        if (! $id) return null;

        return ClassGroup::query()->where('id', $id)->value('name');
    }

    // ==========================================================
    // HELPER — Data Surah
    // ==========================================================

    protected static function getSurahData(): array
    {
        return Cache::remember('alquran_surah', 86400, function () {
            try {
                $response = Http::timeout(10)->get('https://equran.id/api/v2/surat');
                return $response->json()['data'] ?? [];
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    protected static function getSurahMap(): array
    {
        return Cache::remember('alquran_surah_map', 86400, function () {
            $map = [];
            foreach (static::getSurahData() as $item) {
                $nama = $item['namaLatin'] ?? null;
                if (! $nama) continue;

                $map[$nama] = [
                    'nomor'       => $item['nomor'] ?? null,
                    'jumlahAyat'  => (int) ($item['jumlahAyat'] ?? 0),
                    'arti'        => $item['arti'] ?? null,
                    'tempatTurun' => $item['tempatTurun'] ?? null,
                ];
            }
            return $map;
        });
    }

    protected static function surahInfoText(?string $surah): string
    {
        if (! $surah) return 'Pilih surah terlebih dahulu.';

        $info = static::getSurahMap()[$surah] ?? null;
        if (! $info || ! $info['jumlahAyat']) return '';

        return "📖 {$surah} • {$info['jumlahAyat']} ayat"
            . ($info['nomor'] ? " (No. {$info['nomor']})" : '')
            . ($info['arti'] ? " — artinya “{$info['arti']}”" : '');
    }

    protected static function parseAyat(string $input): array
    {
        $ayat = [];
        foreach (explode(',', $input) as $part) {
            $part = trim($part);
            if ($part === '') continue;

            if (str_contains($part, '-')) {
                [$start, $end] = array_map('intval', explode('-', $part, 2));
                if ($start > 0 && $end >= $start) {
                    $ayat = array_merge($ayat, range($start, $end));
                }
            } elseif (is_numeric($part)) {
                $ayat[] = (int) $part;
            }
        }
        return $ayat;
    }

    protected static function validateAyat(?string $surah, ?string $input): ?string
    {
        if (! $surah || ! $input) return null;

        $max = static::getSurahMap()[$surah]['jumlahAyat'] ?? null;
        if (! $max) return null;

        $ayat = static::parseAyat((string) $input);
        if (empty($ayat)) return 'Format ayat tidak valid. Contoh: 1-5 atau 1,3,5.';

        $invalid = array_filter($ayat, fn($a) => $a < 1 || $a > $max);
        if (! empty($invalid)) {
            $invalidList = implode(', ', array_unique($invalid));
            return "Ayat melebihi jumlah surah. {$surah} hanya memiliki {$max} ayat (anda isi: {$invalidList}).";
        }
        return null;
    }

    // ==========================================================
    // HELPER — Auto Kategori
    // ==========================================================

    public static function kategoriDariNilai($nilai): ?string
    {
        if ($nilai === null || $nilai === '') return null;

        $n = (float) $nilai;
        return match (true) {
            $n >= self::AMBANG_L => 'L',
            $n >= self::AMBANG_C => 'C',
            default              => 'TL',
        };
    }

    public static function kategoriLabel(?string $kode): string
    {
        return match ($kode) {
            'L'  => 'L (Lancar ≥ ' . self::AMBANG_L . ')',
            'C'  => 'C (Cukup ' . self::AMBANG_C . '–' . (self::AMBANG_L - 1) . ')',
            'TL' => 'TL (Tidak Lancar < ' . self::AMBANG_C . ')',
            default => '—',
        };
    }

    // ==========================================================
    // HELPER — Auto Rata-rata
    // ==========================================================

    protected static function collectNilaiByJenis(Forms\Get $get): array
    {
        $result = [
            'ziyadah'  => [],
            'murojaah' => [],
            'murottal' => [],
            'tahsin'   => [],
            'tilawah'  => [],
        ];

        foreach (['ziyadah', 'murojaah', 'murottal', 'tahsin', 'tilawah'] as $jenis) {
            foreach ($get("{$jenis}_data") ?? [] as $item) {
                $v = (float) ($item['nilai_penyetoran'] ?? 0);
                if ($v > 0) $result[$jenis][] = $v;
            }
        }

        foreach ($get('data') ?? [] as $item) {
            $jenis = $item['jenis'] ?? null;
            $v = (float) ($item['nilai_penyetoran'] ?? 0);
            if ($jenis && isset($result[$jenis]) && $v > 0) {
                $result[$jenis][] = $v;
            }
        }

        return $result;
    }

    protected static function avg(array $values): ?float
    {
        if (empty($values)) return null;
        return round(array_sum($values) / count($values), 2);
    }

    protected static function autoCalcNilaiPeningkatan(Forms\Get $get, Forms\Set $set): void
    {
        $byJenis = static::collectNilaiByJenis($get);

        $set('nilai_tilawah',  static::avg($byJenis['tilawah']));
        $set('nilai_murottal', static::avg($byJenis['murottal']));
        $set('nilai_tahsin',   static::avg($byJenis['tahsin']));
        $set('nilai_tahfizh',  static::avg(array_merge(
            $byJenis['ziyadah'],
            $byJenis['murojaah'],
        )));
    }

    protected static function formatRataText(Forms\Get $get, string $key): string
    {
        $byJenis = static::collectNilaiByJenis($get);

        [$values, $label] = match ($key) {
            'tilawah'  => [$byJenis['tilawah'], 'Tilawah'],
            'murottal' => [$byJenis['murottal'], 'Murottal'],
            'tahsin'   => [$byJenis['tahsin'],  'Tahsin'],
            'tahfizh'  => [array_merge($byJenis['ziyadah'], $byJenis['murojaah']), 'Ziyadah + Murojaah'],
            default    => [[], '-'],
        };

        if (empty($values)) {
            return "Belum ada setoran {$label}.";
        }

        $avg = round(array_sum($values) / count($values), 2);
        $count = count($values);

        return "📈 Rata-rata {$label}: {$avg} (dari {$count} baris)";
    }

    // ==========================================================
    // HELPER — Group Data per Sesi Input
    // ==========================================================

    public static function groupDataByBatch(array $data): array
    {
        $batches = [];
        foreach ($data as $item) {
            if (! is_array($item)) continue;

            if (! empty($item['_batch_id'])) {
                $key = 'batch_' . $item['_batch_id'];
            } elseif (! empty($item['_created_at'])) {
                $key = 'time_' . $item['_created_at'];
            } else {
                $key = 'legacy';
            }

            $batches[$key][] = $item;
        }

        uasort($batches, function ($a, $b) {
            $ta = $a[0]['_created_at'] ?? '';
            $tb = $b[0]['_created_at'] ?? '';
            return strcmp($ta, $tb);
        });

        return $batches;
    }

    public static function countBatches(array $data): int
    {
        return count(static::groupDataByBatch($data));
    }

    // ═══════════════════════════════════════════════════════════
    // Helper untuk convert kode maqom ke label
    // ═══════════════════════════════════════════════════════════
    public static function maqomLabel(?string $kode): string
    {
        if (! $kode) return '—';

        foreach (self::MAQOM_OPTIONS as $group => $items) {
            if (isset($items[$kode])) {
                return "{$group} — {$items[$kode]}";
            }
        }

        return $kode;
    }

    // ==========================================================
    // HELPER — Repeater Setoran
    // ⭐ Surah HIDDEN untuk Tilawah (auto dari Maqra' Wajib)
    // ==========================================================

    protected static function buildSetoranRepeater(string $fieldName, ?string $forcedJenis = null): Forms\Components\Repeater
    {
        // ⭐ Callback: apakah item ini konteks Tilawah?
        $isTilawahContext = function (Forms\Get $get) use ($forcedJenis): bool {
            if ($forcedJenis !== null) {
                return $forcedJenis === 'tilawah';
            }
            return ($get('jenis') ?? '') === 'tilawah';
        };

        $schema = [
            Forms\Components\Hidden::make('id')->dehydrated(),
            Forms\Components\Hidden::make('jenis')->dehydrated(),
            Forms\Components\Hidden::make('_batch_id')->dehydrated(),
            Forms\Components\Hidden::make('_created_at')->dehydrated(),
        ];

        // ⭐ Maqra' Wajib — hanya untuk Tilawah
        $schema[] = Forms\Components\Select::make('maqra_wajib')
            ->label('Maqra\' Wajib')
            ->options(array_combine(
                array_keys(self::MAQRA_WAJIB),
                array_keys(self::MAQRA_WAJIB)
            ))
            ->placeholder('— Pilih Maqra\' Wajib —')
            ->searchable()
            ->native(false)
            ->live()
            ->afterStateUpdated(function ($state, Forms\Set $set) {
                $preset = self::MAQRA_WAJIB[$state] ?? null;
                if ($preset) {
                    $set('surah', $preset['surah']);
                    $set('ayat', $preset['ayat']);
                }
            })
            ->columnSpan(4)
            ->visible($isTilawahContext)
            ->required($isTilawahContext)
            ->helperText('Pilih untuk auto-isi surah & ayat.');

        // ⭐ Surah — HIDDEN untuk Tilawah (auto dari Maqra' Wajib)
        $schema[] = Forms\Components\Select::make('surah')
            ->label('Surah')
            ->options(fn(): array => collect(static::getSurahData())
                ->pluck('namaLatin', 'namaLatin')->toArray())
            ->searchable()->required()->live()->native(false)->columnSpan(3)
            ->hidden(fn(Forms\Get $get): bool => $isTilawahContext($get))
            ->dehydrated();

        // ⭐ Ayat — columnSpan 5 untuk Tilawah (karena Surah hidden)
        $schema[] = Forms\Components\TextInput::make('ayat')
            ->label('Ayat')
            ->placeholder(fn(Forms\Get $get): string => static::ayatPlaceholder($get('surah')))
            ->required()->live(onBlur: true)
            ->columnSpan(function (Forms\Get $get) use ($isTilawahContext): int {
                return $isTilawahContext($get) ? 5 : 2;
            })
            ->helperText(fn(Forms\Get $get): string => static::surahInfoText($get('surah')))
            ->rules([
                fn(Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                    $error = static::validateAyat($get('surah'), is_string($value) ? $value : null);
                    if ($error) $fail($error);
                },
            ]);

        // ⭐ Maqom — hanya untuk Tilawah
        $schema[] = Forms\Components\Select::make('maqom')
            ->label('Maqom / Lagu')
            ->options(self::MAQOM_OPTIONS)
            ->searchable()
            ->native(false)
            ->columnSpan(4)
            ->visible($isTilawahContext)
            ->helperText('Pilih maqom yang digunakan.');

        // ✅ TOMBOL CEPAT: Hanya muncul untuk Ziyadah (T/C/TL) & Murojaah (B/K)
        $showQuickInput = in_array($forcedJenis, ['ziyadah', 'murojaah']);

        if ($forcedJenis === 'ziyadah') {
            $schema[] = Forms\Components\ToggleButtons::make('nilai_cepat')
                ->label('Pilih Cepat')
                ->options([
                    '100' => 'T', 
                    '75'  => 'C', 
                    '50'  => 'TL'
                ])
                ->colors([
                    '100' => 'success', 
                    '75'  => 'warning', 
                    '50'  => 'danger'
                ])
                ->inline()
                ->live()
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('nilai_penyetoran', $state))
                ->columnSpan(4)
                ->dehydrated(false);
        } elseif ($forcedJenis === 'murojaah') {
            $schema[] = Forms\Components\ToggleButtons::make('nilai_cepat')
                ->label('Pilih Cepat')
                ->options([
                    '100' => 'B', 
                    '50'  => 'K'
                ])
                ->colors([
                    '100' => 'success', 
                    '50'  => 'danger'
                ])
                ->inline()
                ->live()
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('nilai_penyetoran', $state))
                ->columnSpan(4)
                ->dehydrated(false);
        }

        $schema[] = Forms\Components\TextInput::make('nilai_penyetoran')
            ->label('Nilai')->numeric()->minValue(0)->maxValue(100)->step(0.01)
            ->required()->suffix('/100')
            ->columnSpan($showQuickInput ? 3 : 7) // Menyesuaikan lebar jika tombol cepat ada
            ->live(onBlur: true);

        $schema[] = Forms\Components\Placeholder::make('kategori_display')
            ->label('Kategori')
            ->content(fn(Forms\Get $get): string => static::kategoriLabel(static::kategoriDariNilai($get('nilai_penyetoran'))))
            ->columnSpan(2);

        $schema[] = Forms\Components\Textarea::make('catatan')
            ->label('Catatan')->rows(2)->maxLength(500)->nullable()->columnSpanFull();

        return Forms\Components\Repeater::make($fieldName)
            ->hiddenLabel()
            ->live(debounce: 500)
            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                static::autoCalcNilaiPeningkatan($get, $set);
            })
            ->schema($schema)
            ->columns(9)
            ->addActionLabel('+ Tambah Setoran')
            ->defaultItems(1)->reorderable()->collapsible()
            ->itemLabel(function (array $state) use ($forcedJenis): ?string {
                $label = $state['surah'] ?? 'Setoran Baru';
                if (! empty($state['ayat'])) $label .= " — Ayat {$state['ayat']}";

                if (! empty($state['maqom'])) {
                    $maqomLabel = self::maqomLabel($state['maqom']);
                    $label .= " • {$maqomLabel}";
                }

                if (! empty($state['_created_at'])) {
                    try {
                        $tgl = Carbon::parse($state['_created_at'])->isoFormat('D MMM, HH:mm');
                        $label .= "   🕒 {$tgl}";
                    } catch (\Throwable $e) {
                    }
                }
                return $label;
            });
    }

    protected static function ayatPlaceholder(?string $surah): string
    {
        if (! $surah) return 'Contoh: 1-5';
        $max = static::getSurahMap()[$surah]['jumlahAyat'] ?? 0;
        return $max ? "1-{$max} (contoh: 1-5)" : 'Contoh: 1-5';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penilaian')
                    ->description('Pilih kelas dan santri.')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Select::make('class_group_id')
                            ->label('Kelas')
                            ->options(fn(): array => ClassGroup::query()
                                ->select(['id', 'name'])->pluck('name', 'id')->toArray())
                            ->required()->searchable()->preload()->native(false)->live()
                            ->disabledOn('edit')->dehydrated(),

                        Forms\Components\Select::make('user_id')
                            ->label('Nama Santri')
                            ->options(function (Forms\Get $get): array {
                                $id = $get('class_group_id');
                                if (! $id) return [];
                                return ClassGroup::find($id)
                                    ?->students()->pluck('users.name', 'users.id')->toArray() ?? [];
                            })
                            ->required()->searchable()->preload()->native(false)->live()
                            ->disabledOn('edit')->dehydrated()
                            ->helperText('Pilih kelas terlebih dahulu.'),

                        Forms\Components\Placeholder::make('assessment_type_info')
                            ->label('Jenis Penilaian')
                            ->content(function (?Assessment $record): string {
                                if (! $record || ! $record->assessment_type) return '—';
                                return collect(explode(',', $record->assessment_type))
                                    ->map(fn($j) => self::JENIS_LABELS_LONG[trim($j)] ?? ucfirst(trim($j)))
                                    ->filter()->implode(' + ');
                            })
                            ->visible(fn(string $operation): bool => $operation === 'edit'),

                        Forms\Components\Hidden::make('month')->default(now()->month),
                        Forms\Components\Hidden::make('year')->default(now()->year),

                        Forms\Components\Placeholder::make('existing_warning')
                            ->hiddenLabel()
                            ->content(function (Forms\Get $get): ?string {
                                $classId = $get('class_group_id');
                                $userId  = $get('user_id');
                                if (! $classId || ! $userId) return null;

                                $month = $get('month') ?? now()->month;
                                $year  = $get('year')  ?? now()->year;

                                $existing = \App\Features\Grades\Models\Assessment::query()
                                    ->where('class_group_id', $classId)
                                    ->where('user_id', $userId)
                                    ->where('month', $month)
                                    ->where('year', $year)->first();

                                if (! $existing) return null;

                                $data = is_array($existing->data) ? $existing->data : [];
                                $count = count($data);
                                $batches = static::countBatches($data);

                                $bulan = match ((int) $month) {
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember',
                                    default => '—',
                                };

                                return "⚠️ Sudah ada {$count} setoran ({$batches}× input) untuk santri ini "
                                    . "di bulan {$bulan} {$year}. Setoran baru akan **ditambahkan** ke record yang sama.";
                            })
                            ->visible(fn(Forms\Get $get, string $operation): bool =>
                            $operation === 'create' && $get('class_group_id') && $get('user_id'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pilih Jenis Setoran')
                    ->description('Centang jenis setoran yang akan diinput. Kolom input akan muncul di bawah.')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Forms\Components\CheckboxList::make('jenis_aktif')
                            ->label('Jenis Setoran')
                            ->options(self::JENIS_LABELS_LONG)
                            ->columns(5)->live()->default(['ziyadah']),
                    ])
                    ->visible(fn(string $operation): bool => $operation === 'create'),

                Forms\Components\Section::make('📖 Ziyadah')
                    ->description('Hafalan baru — setoran yang baru dihafal.')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->schema([static::buildSetoranRepeater('ziyadah_data', 'ziyadah')])
                    ->visible(fn(Forms\Get $get, string $operation): bool =>
                    $operation === 'create' && in_array('ziyadah', $get('jenis_aktif') ?? []))
                    ->collapsible(),

                Forms\Components\Section::make('🔄 Murojaah')
                    ->description('Mengulang hafalan lama.')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([static::buildSetoranRepeater('murojaah_data', 'murojaah')])
                    ->visible(fn(Forms\Get $get, string $operation): bool =>
                    $operation === 'create' && in_array('murojaah', $get('jenis_aktif') ?? []))
                    ->collapsible(),

                Forms\Components\Section::make('🎵 Murottal')
                    ->description("Membaca Al-Qur'an dengan tartil.")
                    ->icon('heroicon-o-musical-note')
                    ->schema([static::buildSetoranRepeater('murottal_data', 'murottal')])
                    ->visible(fn(Forms\Get $get, string $operation): bool =>
                    $operation === 'create' && in_array('murottal', $get('jenis_aktif') ?? []))
                    ->collapsible(),

                Forms\Components\Section::make('🎯 Tahsin')
                    ->description('Perbaikan bacaan / makhraj.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([static::buildSetoranRepeater('tahsin_data', 'tahsin')])
                    ->visible(fn(Forms\Get $get, string $operation): bool =>
                    $operation === 'create' && in_array('tahsin', $get('jenis_aktif') ?? []))
                    ->collapsible(),

                Forms\Components\Section::make('📚 Tilawah')
                    ->description("Membaca Al-Qur'an — lengkap dengan Maqra' Wajib & Maqom.")
                    ->icon('heroicon-o-book-open')
                    ->schema([static::buildSetoranRepeater('tilawah_data', 'tilawah')])
                    ->visible(fn(Forms\Get $get, string $operation): bool =>
                    $operation === 'create' && in_array('tilawah', $get('jenis_aktif') ?? []))
                    ->collapsible(),

                Forms\Components\Section::make('📊 Nilai Peningkatan (Untuk Rapor)')
                    ->description('Otomatis dihitung dari rata-rata setoran. Field yang muncul menyesuaikan jenis kelas.')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('hitungUlang')
                                ->label('🔄 Hitung Ulang dari Setoran')
                                ->color('warning')->size('sm')->icon('heroicon-m-arrow-path')
                                ->action(function (Forms\Get $get, Forms\Set $set) {
                                    static::autoCalcNilaiPeningkatan($get, $set);
                                    Notification::make()->success()
                                        ->title('Nilai berhasil dihitung ulang')->send();
                                }),
                        ]),

                        Forms\Components\TextInput::make('nilai_tilawah')
                            ->label('Nilai Tilawah')
                            ->numeric()->minValue(0)->maxValue(100)->nullable()
                            ->placeholder('Auto dari setoran Tilawah')->suffix('/ 100')
                            ->visible(fn(Forms\Get $get): bool =>
                            static::kelasPunyaMapel(static::classNameFromForm($get), 'Tilawah'))
                            ->helperText(fn(Forms\Get $get): string =>
                            static::formatRataText($get, 'tilawah')),

                        Forms\Components\TextInput::make('nilai_murottal')
                            ->label('Nilai Murottal')
                            ->numeric()->minValue(0)->maxValue(100)->nullable()
                            ->placeholder('Auto dari setoran Murottal')->suffix('/ 100')
                            ->visible(fn(Forms\Get $get): bool =>
                            static::kelasPunyaMapel(static::classNameFromForm($get), 'Murottal'))
                            ->helperText(fn(Forms\Get $get): string =>
                            static::formatRataText($get, 'murottal')),

                        Forms\Components\TextInput::make('nilai_tahsin')
                            ->label('Nilai Tahsin')
                            ->numeric()->minValue(0)->maxValue(100)->nullable()
                            ->placeholder('Auto dari setoran Tahsin')->suffix('/ 100')
                            ->visible(fn(Forms\Get $get): bool =>
                            static::kelasPunyaMapel(static::classNameFromForm($get), 'Tahsin'))
                            ->helperText(fn(Forms\Get $get): string =>
                            static::formatRataText($get, 'tahsin')),

                        Forms\Components\TextInput::make('nilai_tahfizh')
                            ->label('Nilai Tahfizh')
                            ->numeric()->minValue(0)->maxValue(100)->nullable()
                            ->placeholder('Auto dari Ziyadah + Murojaah')->suffix('/ 100')
                            ->visible(fn(Forms\Get $get): bool =>
                            static::kelasPunyaMapel(static::classNameFromForm($get), 'Tahfizh'))
                            ->helperText(fn(Forms\Get $get): string =>
                            static::formatRataText($get, 'tahfizh')),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Detail Setoran')
                    ->description('Edit setoran yang sudah ada. Setoran dari input berbeda ditandai dengan 🕒 waktu input.')
                    ->icon('heroicon-o-book-open')
                    ->schema([static::buildSetoranRepeater('data', null)])
                    ->visible(fn(string $operation): bool => $operation === 'edit'),
            ]);
    }

    // ==========================================================
    // INFOLIST
    // ==========================================================

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Informasi Penilaian')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Infolists\Components\TextEntry::make('classGroup.name')
                        ->label('Kelas')->badge()->color('info'),
                    Infolists\Components\TextEntry::make('student.name')
                        ->label('Nama Santri')->weight('bold')->icon('heroicon-m-user-circle'),
                    Infolists\Components\TextEntry::make('classGroup.teacher.name')
                        ->label('Ustad Pengampu')->placeholder('—')->icon('heroicon-m-academic-cap'),
                    Infolists\Components\TextEntry::make('assessment_type')
                        ->label('Jenis Penilaian')->badge()
                        ->state(fn(Assessment $record): array =>
                        collect(explode(',', $record->assessment_type ?? ''))
                            ->map(fn($j) => trim($j))->filter()
                            ->map(fn($j) => self::JENIS_LABELS[$j] ?? ucfirst($j))->toArray()),
                    Infolists\Components\TextEntry::make('month')
                        ->label('Bulan')
                        ->formatStateUsing(fn(?int $state): string => match ((int) $state) {
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                            default => '—',
                        }),
                    Infolists\Components\TextEntry::make('year')->label('Tahun'),
                    Infolists\Components\TextEntry::make('_summary')
                        ->label('Total')->badge()->color('primary')
                        ->state(function (Assessment $record): string {
                            $data = is_array($record->data) ? $record->data : [];
                            return count($data) . ' setoran • ' . self::countBatches($data) . '× input';
                        }),
                ])->columns(3),

            Infolists\Components\Section::make('Detail Hafalan / Bacaan')
                ->description(fn(Assessment $record): string => (function () use ($record) {
                    $data = is_array($record->data) ? $record->data : [];
                    return count($data) . ' setoran dari ' . self::countBatches($data) . ' sesi input.';
                })())
                ->icon('heroicon-o-book-open')
                ->schema(fn(Assessment $record): array => static::renderBatchSections($record))
                ->columnSpanFull(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // Render batch sections via HTML string
    // ═══════════════════════════════════════════════════════════
    protected static function renderBatchSections(Assessment $record): array
    {
        $data = is_array($record->data) ? $record->data : [];
        $batches = static::groupDataByBatch($data);

        if (empty($batches)) {
            return [
                Infolists\Components\TextEntry::make('_empty')
                    ->hiddenLabel()
                    ->state('Belum ada setoran tercatat.')
                    ->color('gray')
            ];
        }

        $schema = [];
        $no = 1;

        foreach ($batches as $items) {
            $first = $items[0];
            $time = $first['_created_at'] ?? null;

            $title = "🕒 Input #{$no}";
            $desc = count($items) . ' setoran';

            if ($time) {
                try {
                    $carbon = Carbon::parse($time);
                    $title = '🕒 Input #' . $no . ' — ' . $carbon->isoFormat('D MMM YYYY, HH:mm');
                    $desc = count($items) . ' setoran • ' . $carbon->diffForHumans();
                } catch (\Throwable $e) {
                }
            }

            // Cek apakah ada maqom di batch ini
            $hasMaqom = collect($items)->contains(fn($i) => ! empty($i['maqom']));
            $colspan = $hasMaqom ? 6 : 5;

            // Bangun HTML tabel langsung
            $html = '<table style="width:100%; border-collapse:collapse; font-size:11px;">';

            // Header
            $html .= '<thead><tr style="background-color:#e8f3f0; color:#1a3c34;">';
            $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:left; width:12%;">Jenis</th>';
            $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:left;">Surah</th>';
            $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center; width:10%;">Ayat</th>';
            if ($hasMaqom) {
                $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:left; width:20%;">Maqom</th>';
            }
            $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center; width:10%;">Nilai</th>';
            $html .= '<th style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center; width:20%;">Kategori</th>';
            $html .= '</tr></thead><tbody>';

            // Body
            foreach ($items as $item) {
                $jenis      = $item['jenis'] ?? '—';
                $jenisLabel = self::JENIS_LABELS[$jenis] ?? ucfirst($jenis);
                $jenisColor = self::JENIS_COLORS[$jenis] ?? 'gray';

                $jenisBg = match ($jenisColor) {
                    'success' => '#dcfce7',
                    'info'    => '#dbeafe',
                    'primary' => '#ede9fe',
                    'warning' => '#fef3c7',
                    'danger'  => '#fee2e2',
                    default   => '#f3f4f6',
                };
                $jenisText = match ($jenisColor) {
                    'success' => '#166534',
                    'info'    => '#1e40af',
                    'primary' => '#6b21a8',
                    'warning' => '#92400e',
                    'danger'  => '#991b1b',
                    default   => '#374151',
                };

                $nilaiKode     = $item['nilai'] ?? null;
                $kategoriLabel = self::kategoriLabel($nilaiKode);
                $kategoriColor = match ($nilaiKode) {
                    'L'  => '#166534',
                    'C'  => '#92400e',
                    'TL' => '#991b1b',
                    default => '#6b7280',
                };

                $html .= '<tr>';
                $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px;">'
                    . '<span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:600; background-color:' . $jenisBg . '; color:' . $jenisText . ';">'
                    . e($jenisLabel) . '</span></td>';
                $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px;">' . e($item['surah'] ?? '—') . '</td>';
                $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center;">' . e($item['ayat'] ?? '—') . '</td>';

                if ($hasMaqom) {
                    $maqomText = ! empty($item['maqom']) ? self::maqomLabel($item['maqom']) : '—';
                    $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px;">' . e($maqomText) . '</td>';
                }

                $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center; font-weight:bold;">' . e($item['nilai_penyetoran'] ?? '—') . '</td>';
                $html .= '<td style="border:1px solid #c5d6d1; padding:6px 8px; text-align:center; font-weight:600; color:' . $kategoriColor . ';">' . e($kategoriLabel) . '</td>';
                $html .= '</tr>';

                // Catatan
                if (! empty($item['catatan'])) {
                    $html .= '<tr><td colspan="' . $colspan . '" style="border:1px solid #c5d6d1; padding:4px 8px; background-color:#fafafa; font-size:10px; color:#666;">'
                        . '<strong>Catatan:</strong> ' . e($item['catatan']) . '</td></tr>';
                }
            }

            $html .= '</tbody></table>';

            $schema[] = Infolists\Components\Section::make($title)
                ->description($desc)
                ->compact()
                ->schema([
                    Infolists\Components\TextEntry::make("batch_table_{$no}")
                        ->hiddenLabel()
                        ->html()
                        ->state($html),
                ]);
            $no++;
        }

        return $schema;
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Santri')->searchable()->sortable()
                    ->weight('semibold')->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('classGroup.name')
                    ->label('Kelas')->badge()
                    ->icon(fn(?string $state): string => match (true) {
                        str_contains($state ?? '', 'Tahfizh')  => 'heroicon-m-bookmark',
                        str_contains($state ?? '', 'Tahsin')   => 'heroicon-m-academic-cap',
                        str_contains($state ?? '', 'Tilawah')  => 'heroicon-m-book-open',
                        str_contains($state ?? '', 'Murottal') => 'heroicon-m-musical-note',
                        default => 'heroicon-m-user-group',
                    })
                    ->color(fn(?string $state): string => match (true) {
                        $state === null || $state === '' => 'gray',
                        str_contains($state, 'Tahfizh')  => 'primary',
                        str_contains($state, 'Tahsin')   => 'warning',
                        str_contains($state, 'Tilawah')  => 'success',
                        str_contains($state, 'Murottal') => 'info',
                        default => 'gray',
                    })
                    ->searchable()->sortable()->placeholder('—')->toggleable(),

                Tables\Columns\TextColumn::make('setoran_count')
                    ->label('Setoran')->alignCenter()->badge()->color('primary')
                    ->icon('heroicon-m-document-text')
                    ->getStateUsing(fn(Assessment $record): int =>
                    is_array($record->data) ? count($record->data) : 0),

                Tables\Columns\TextColumn::make('batch_count')
                    ->label('Sesi')->alignCenter()->badge()->color('info')
                    ->icon('heroicon-m-clock')
                    ->getStateUsing(fn(Assessment $record): int =>
                    self::countBatches(is_array($record->data) ? $record->data : [])),

                Tables\Columns\TextColumn::make('assessment_type')
                    ->label('Jenis')->badge()->separator(',')
                    ->formatStateUsing(fn(?string $state): string =>
                    self::JENIS_LABELS[trim($state ?? '')] ?? ucfirst(trim($state ?? '—')))
                    ->color(fn(?string $state): string =>
                    self::JENIS_COLORS[trim($state ?? '')] ?? 'gray')
                    ->icon(fn(?string $state): string =>
                    self::JENIS_ICONS[trim($state ?? '')] ?? 'heroicon-m-question-mark-circle'),

                Tables\Columns\TextColumn::make('classGroup.teacher.name')
                    ->label('Ustad')->icon('heroicon-m-academic-cap')
                    ->placeholder('—')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('month')
                    ->label('Periode')
                    ->formatStateUsing(fn($state, Assessment $record): string => match ((int) $state) {
                        1 => 'Jan',
                        2 => 'Feb',
                        3 => 'Mar',
                        4 => 'Apr',
                        5 => 'Mei',
                        6 => 'Jun',
                        7 => 'Jul',
                        8 => 'Ags',
                        9 => 'Sep',
                        10 => 'Okt',
                        11 => 'Nov',
                        12 => 'Des',
                        default => '—',
                    } . ' ' . $record->year)
                    ->badge()->color('gray')->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')->dateTime('d M Y H:i')
                    ->description(fn(Assessment $record): string => $record->created_at?->diffForHumans() ?? '')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')->dateTime('d M Y H:i')
                    ->description(fn(Assessment $record): string => $record->updated_at?->diffForHumans() ?? '')
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assessment_type')
                    ->label('Jenis Penilaian')->options(self::JENIS_LABELS)->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) return $query;
                        return $query->where(function ($q) use ($data) {
                            foreach ($data['values'] as $jenis) {
                                $q->orWhere('assessment_type', $jenis)
                                    ->orWhere('assessment_type', 'like', "{$jenis},%")
                                    ->orWhere('assessment_type', 'like', "%,{$jenis}")
                                    ->orWhere('assessment_type', 'like', "%,{$jenis},%");
                            }
                        });
                    }),

                Tables\Filters\SelectFilter::make('class_group_id')
                    ->label('Kelas')->relationship('classGroup', 'name')
                    ->searchable()->preload()->multiple(),

                Tables\Filters\Filter::make('month_year')
                    ->form([
                        Forms\Components\Select::make('month')->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])->native(false),
                        Forms\Components\TextInput::make('year')->label('Tahun')
                            ->numeric()->default(now()->year),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['month'], fn($q, $m) => $q->where('month', $m))
                        ->when($data['year'], fn($q, $y) => $q->where('year', $y)))
                    ->label('Periode (Bulan/Tahun)'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip('Lihat Detail')->modalWidth('5xl'),
                Tables\Actions\EditAction::make()->iconButton()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Penilaian?')
                    ->modalDescription(fn(Assessment $record): string =>
                    "Penilaian untuk \"{$record->student?->name}\" akan dihapus permanen."),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc')->striped()
            ->paginated([10, 25, 50, 100])->defaultPaginationPageOption(10)
            ->emptyStateHeading('Belum Ada Penilaian')
            ->emptyStateDescription('Input penilaian pertama dengan klik tombol di atas.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Input Penilaian')->icon('heroicon-o-plus'),
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
            return $query->whereHas('classGroup', fn($q) => $q->where('teacher_id', $user->id));
        }
        return $query;
    }

    // ==========================================================
    // AUTHORIZATION
    // ==========================================================

    protected static function canAccessRecord($record, string $action): bool
    {
        if (! static::canAccessAction($action)) return false;

        $user = auth()->user();
        if ($user && $user->role === 'guru') {
            return (int) $record->classGroup?->teacher_id === (int) $user->id;
        }
        return true;
    }

    public static function canView($record): bool
    {
        return static::canAccessRecord($record, 'view');
    }
    public static function canEdit($record): bool
    {
        return static::canAccessRecord($record, 'update');
    }
    public static function canDelete($record): bool
    {
        return static::canAccessRecord($record, 'delete');
    }

    // ==========================================================
    // PAGES
    // ==========================================================

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'view'   => Pages\ViewAssessment::route('/{record}'),
            'edit'   => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}