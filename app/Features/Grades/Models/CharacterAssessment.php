<?php

namespace App\Features\Grades\Models;

use App\Features\Academic\Models\ClassGroup;
use App\Features\Academic\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_group_id',
        'semester_id',
        'assessor_id',
        'adab_islam',
        'kerjasama',
        'tanggung_jawab',
        'kedisiplinan',
        'kejujuran',
        'notes',
    ];

    protected $casts = [
        'adab_islam'      => 'integer',
        'kerjasama'       => 'integer',
        'tanggung_jawab'  => 'integer',
        'kedisiplinan'    => 'integer',
        'kejujuran'       => 'integer',
    ];

    // ==========================================================
    // KONSTANTA — Daftar aspek karakter
    // ==========================================================

    public const ASPECTS = [
        'adab_islam'     => 'Adab Islam',
        'kerjasama'      => 'Kerjasama',
        'tanggung_jawab' => 'Tanggung Jawab',
        'kedisiplinan'   => 'Kedisiplinan',
        'kejujuran'      => 'Kejujuran',
    ];

    // ==========================================================
    // RELASI
    // ==========================================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    // ==========================================================
    // ACCESSOR — Hitung otomatis
    // ==========================================================

    /**
     * Rata-rata dari 5 aspek karakter.
     */
    public function getAverageScoreAttribute(): float
    {
        $scores = [
            $this->adab_islam,
            $this->kerjasama,
            $this->tanggung_jawab,
            $this->kedisiplinan,
            $this->kejujuran,
        ];

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Bintang untuk rata-rata (1-5).
     */
    public function getAverageStarsAttribute(): int
    {
        return $this->scoreToStars($this->average_score);
    }

    /**
     * Convert nilai 0-100 ke bintang 1-5.
     */
    public static function scoreToStars(float $score): int
    {
        return match (true) {
            $score >= 90 => 5,
            $score >= 80 => 4,
            $score >= 70 => 3,
            $score >= 60 => 2,
            default      => 1,
        };
    }

    /**
     * Ambil nilai per aspek dalam format array untuk view.
     * Return: [['label' => 'Adab Islam', 'score' => 95, 'stars' => 5], ...]
     */
    public function getAspectRowsAttribute(): array
    {
        $rows = [];

        foreach (self::ASPECTS as $field => $label) {
            $score = (int) $this->{$field};
            $rows[] = [
                'label' => $label,
                'score' => $score,
                'stars' => self::scoreToStars($score),
            ];
        }

        return $rows;
    }
}