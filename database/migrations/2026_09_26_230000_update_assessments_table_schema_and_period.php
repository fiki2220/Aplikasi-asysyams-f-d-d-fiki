<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Update skema assessments — tambah periode (bulan/tahun)
 * dan unique constraint baru.
 * ------------------------------------------------------------
 * File: 2026_09_26_230000_update_assessments_table_schema_and_period.php
 *
 * Perubahan:
 *   1. Tambah kolom `month` & `year` (nullable)
 *   2. Update ENUM `assessment_type` — tambah `tahfizh`, `tajwid`
 *   3. Drop unique index lama (single period)
 *      → assessments_class_group_id_user_id_assessment_type_unique
 *   4. Buat unique index baru (multi period)
 *      → assessments_period_unique
 *
 * ⚠️ KENAPA try/catch DI DALAM Schema::table TIDAK BEKERJA:
 *    Blueprint hanya MENGHIMPUN perintah. SQL baru dijalankan
 *    setelah closure selesai — di luar scope try/catch. Jadi
 *    error DROP INDEX tidak akan pernah ter-catch.
 *
 *    Solusi: cek dulu via information_schema (`indexExists()`),
 *    lalu drop via `DB::statement()` (eksekusi langsung).
 * ------------------------------------------------------------
 */
return new class extends Migration
{
    /** @var string Nama index unique lama (single periode) */
    private string $oldIndex = 'assessments_class_group_id_user_id_assessment_type_unique';

    /** @var string Nama index unique baru (dengan month + year) */
    private string $newIndex = 'assessments_period_unique';

    /** @var array<string> Daftar kolom yang ditambahkan migration ini */
    private array $columns = ['month', 'year'];

    /** @var array<string> Nilai ENUM setelah upgrade */
    private array $enumAfter = ['ziyadah', 'murojaah', 'tahsin', 'tilawah', 'tahfizh', 'tajwid'];

    /** @var array<string> Nilai ENUM sebelum upgrade (untuk rollback) */
    private array $enumBefore = ['ziyadah', 'murojaah', 'tahsin', 'tilawah'];

    // ================================================================
    // UP — Jalankan migrasi
    // ================================================================
    public function up(): void
    {
        $this->addPeriodColumns();
        $this->updateAssessmentTypeEnum($this->enumAfter);
        $this->dropOldUniqueIndex();
        $this->createNewUniqueIndex();
    }

    // ================================================================
    // DOWN — Rollback migrasi
    // ================================================================
    public function down(): void
    {
        $this->dropNewUniqueIndex();
        $this->restoreOldUniqueIndex();
        $this->dropPeriodColumns();
        $this->updateAssessmentTypeEnum($this->enumBefore);
    }

    // ================================================================
    // STEP 1 — Tambah kolom `month` & `year`
    // ================================================================
    private function addPeriodColumns(): void
    {
        // Pisah jadi 2 closure supaya urutan ALTER TABLE di MySQL
        // tidak "ketuker" saat `after('month')` dijalankan sebelum
        // `month` benar-benar ada di DB.
        if (! Schema::hasColumn('assessments', 'month')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->unsignedTinyInteger('month')
                      ->nullable()
                      ->after('assessment_type')
                      ->comment('Bulan penilaian (1-12)');
            });
        }

        if (! Schema::hasColumn('assessments', 'year')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->unsignedSmallInteger('year')
                      ->nullable()
                      ->after('month')
                      ->comment('Tahun penilaian (mis. 2026)');
            });
        }
    }

    // ================================================================
    // STEP 2 — Update ENUM `assessment_type`
    // ================================================================
    private function updateAssessmentTypeEnum(array $values): void
    {
        $enumList = collect($values)
            ->map(fn ($v) => "'{$v}'")
            ->implode(',');

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE assessments
                 MODIFY COLUMN assessment_type ENUM({$enumList}) NOT NULL"
            );
            return;
        }

        // Fallback PostgreSQL / SQLite — coba ubah ke string biasa
        try {
            Schema::table('assessments', function (Blueprint $table) {
                $table->string('assessment_type')->change();
            });
        } catch (\Throwable $e) {
            // Beberapa driver tidak support ->change(); abaikan
        }
    }

    // ================================================================
    // STEP 3 — Drop unique index lama (kalau ada)
    // ================================================================
    private function dropOldUniqueIndex(): void
    {
        if (! $this->indexExists('assessments', $this->oldIndex)) {
            return; // sudah tidak ada — skip
        }

        DB::statement("ALTER TABLE assessments DROP INDEX `{$this->oldIndex}`");
    }

    // ================================================================
    // STEP 4 — Buat unique index baru (kalau belum ada)
    // ================================================================
    private function createNewUniqueIndex(): void
    {
        if ($this->indexExists('assessments', $this->newIndex)) {
            return; // sudah ada — skip
        }

        Schema::table('assessments', function (Blueprint $table) {
            $table->unique(
                ['class_group_id', 'user_id', 'assessment_type', 'month', 'year'],
                $this->newIndex
            );
        });
    }

    // ================================================================
    // DOWN helpers
    // ================================================================

    private function dropNewUniqueIndex(): void
    {
        if (! $this->indexExists('assessments', $this->newIndex)) {
            return;
        }
        DB::statement("ALTER TABLE assessments DROP INDEX `{$this->newIndex}`");
    }

    private function restoreOldUniqueIndex(): void
    {
        if ($this->indexExists('assessments', $this->oldIndex)) {
            return;
        }

        Schema::table('assessments', function (Blueprint $table) {
            $table->unique(
                ['class_group_id', 'user_id', 'assessment_type'],
                $this->oldIndex
            );
        });
    }

    private function dropPeriodColumns(): void
    {
        // Drop dalam SATU closure — aman karena keduanya dicek dulu.
        $existing = array_filter(
            $this->columns,
            fn ($col) => Schema::hasColumn('assessments', $col)
        );

        if (empty($existing)) {
            return;
        }

        Schema::table('assessments', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }

    // ================================================================
    // HELPER — Cek keberadaan index via information_schema
    // ================================================================
    /**
     * Cek apakah index ada di tabel tertentu.
     * ------------------------------------------------------------
     * Laravel < 10.36 tidak punya `Schema::hasIndex()` native,
     * jadi kita query langsung ke `information_schema.statistics`.
     *
     * Kalau Laravel kamu ≥ 10.36, bisa diganti dengan:
     *   return Schema::hasIndex($table, $indexName);
     * ------------------------------------------------------------
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $dbName     = $connection->getDatabaseName();

        // PostgreSQL pakai nama schema berbeda
        if ($connection->getDriverName() === 'pgsql') {
            $result = $connection->selectOne(
                "SELECT COUNT(*) AS count
                 FROM pg_indexes
                 WHERE schemaname = 'public'
                   AND tablename  = ?
                   AND indexname  = ?",
                [$table, $indexName]
            );
            return (int) ($result->count ?? 0) > 0;
        }

        // SQLite: query sqlite_master
        if ($connection->getDriverName() === 'sqlite') {
            $result = $connection->selectOne(
                "SELECT COUNT(*) AS count
                 FROM sqlite_master
                 WHERE type = 'index'
                   AND tbl_name = ?
                   AND name = ?",
                [$table, $indexName]
            );
            return (int) ($result->count ?? 0) > 0;
        }

        // MySQL / MariaDB (default)
        $result = $connection->selectOne(
            "SELECT COUNT(*) AS count
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name   = ?
               AND index_name   = ?",
            [$dbName, $table, $indexName]
        );

        return (int) ($result->count ?? 0) > 0;
    }
};