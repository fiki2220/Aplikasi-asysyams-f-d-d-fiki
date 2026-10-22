<?php

namespace App\Features\SiteSettings\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * SiteSetting Model
 * ============================================================
 * Menyimpan pengaturan website dalam bentuk key-value.
 *
 * Kolom `value` bisa berisi:
 *   • JSON array  → untuk gambar (multi upload) & video (1 file)
 *   • datetime    → untuk deadline ("2026-10-09 00:59")
 *   • time        → untuk batas absen ("16:40")
 *   • string      → untuk teks biasa
 *
 * Accessor di bawah otomatis decode/encode JSON sehingga
 * `$setting->value` mengembalikan array kalau memang array.
 * ============================================================
 */
class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    // ══════════════════════════════════════════════════════════════
    // ⭐ ACCESSOR UTAMA — kolom `value`
    // ══════════════════════════════════════════════════════════════
    /**
     * Otomatis decode JSON saat BACA dan encode saat SIMPAN.
     * ------------------------------------------------------------
     * • Baca  : string JSON valid → array
     *           string biasa      → string
     * • Simpan: array             → JSON string
     *           string            → string as-is
     * ------------------------------------------------------------
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value) || $value === '') {
                    return null;
                }

                // Kalau sudah array (mis. dari cast lain) → langsung return
                if (is_array($value)) {
                    return $value;
                }

                // Coba decode sebagai JSON
                $decoded = json_decode($value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                // Bukan JSON → return string asli
                return $value;
            },
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

                return $value;
            }
        );
    }

    // ══════════════════════════════════════════════════════════════
    // ACCESSOR KOMPATIBILITAS — untuk Filament Resource
    // ══════════════════════════════════════════════════════════════
    // Alias yang dipakai SiteSettingResource:
    //   • value_images   → FileUpload multiple (gambar)
    //   • value_video    → FileUpload single (video)
    //   • value_deadline → DateTimePicker
    //   • value_time     → TimePicker
    //   • value_text     → Textarea

    /**
     * Alias `value_images` → untuk FileUpload multiple (gambar).
     * Selalu return array path gambar.
     */
    public function getValueImagesAttribute(): array
    {
        return $this->normalizeToArray($this->value);
    }

    /**
     * ⭐ Alias `value_video` → untuk FileUpload single video.
     * Selalu return single path string (atau null).
     */
    public function getValueVideoAttribute(): ?string
    {
        return $this->normalizeToSingle($this->value);
    }

    /**
     * Alias `value_deadline` → untuk DateTimePicker.
     */
    public function getValueDeadlineAttribute()
    {
        return $this->value;
    }

    /**
     * Alias `value_time` → untuk TimePicker.
     * Otomatis ambil "H:i" dari string datetime apapun.
     */
    public function getValueTimeAttribute(): ?string
    {
        $value = $this->value;

        if (empty($value)) {
            return null;
        }

        // Sudah format H:i?
        if (is_string($value) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
            // Trim detik kalau ada → "16:40:00" jadi "16:40"
            return substr($value, 0, 5);
        }

        // Coba parse sebagai datetime
        try {
            return \Carbon\Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Alias `value_text` → untuk Textarea.
     */
    public function getValueTextAttribute(): ?string
    {
        $value = $this->value;

        return is_string($value) ? $value : null;
    }

    // ══════════════════════════════════════════════════════════════
    // HELPER — Ambil URL publik file
    // ══════════════════════════════════════════════════════════════

    /**
     * URL publik file pertama.
     * Berguna di Blade:
     *   <img src="{{ $setting->first_url }}">
     *   <video src="{{ $setting->first_url }}">
     *
     * @param  string  $disk  Nama disk (default: 'public')
     */
    public function getFirstUrlAttribute(string $disk = 'public'): ?string
    {
        $path = $this->normalizeToSingle($this->value);

        if (! $path) {
            return null;
        }

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ══════════════════════════════════════════════════════════════
    // HELPER — Normalisasi value
    // ══════════════════════════════════════════════════════════════

    /**
     * Normalisasi value → array of paths.
     * Handle: array, JSON string, single string.
     */
    protected function normalizeToArray($value): array
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

    /**
     * Normalisasi value → single path string (atau null).
     * Handle: array (ambil pertama), JSON string, single string.
     */
    protected function normalizeToSingle($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $first = reset($value);
            return $first ? (string) $first : null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                $first = reset($decoded);
                return $first ? (string) $first : null;
            }

            return $value;
        }

        return null;
    }
}