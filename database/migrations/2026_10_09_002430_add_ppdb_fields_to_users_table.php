<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Detail santri & data PPDB untuk tabel users.
 * ------------------------------------------------------------
 * File: 2025_11_25_141406_add_student_details_to_users_table.php
 *
 * Kolom yang ditambahkan:
 *   • grade_level   → kelas/jenjang yang dituju
 *   • birth_date    → tanggal lahir santri
 *   • birth_place   → tempat lahir santri
 *   • gender        → L / P (dipindah ke sini)
 *   • nisn          → NISN santri (unique)
 *   • phone         → no HP santri (opsional)
 *   • mother_name   → nama ibu kandung
 *   • father_name   → nama ayah
 *   • parent_name   → nama wali (opsional)
 *   • parent_phone  → no HP orang tua / wali
 *   • school_origin → asal sekolah sebelumnya
 *   • address       → alamat lengkap
 *
 * ⚠️ PENTING: Migration ini mengubah struktur besar-besaran.
 *    Kalau sudah pernah dijalankan, WAJIB `migrate:fresh`
 *    (atau hapus migration `2025_11_25_144646_add_gender_to_users`
 *    untuk hindari duplikat gender).
 * ------------------------------------------------------------
 */
return new class extends Migration
{
    /**
     * Daftar kolom + urutan penambahannya.
     * Dipakai untuk guard `hasColumn()` di up() & down().
     */
    private array $columns = [
        'grade_level',
        'birth_date',
        'birth_place',
        'gender',
        'nisn',
        'phone',
        'mother_name',
        'father_name',
        'parent_name',
        'parent_phone',
        'school_origin',
        'address',
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'grade_level')) {
                $table->string('grade_level', 50)->nullable()->after('email')
                      ->comment('Kelas / Jenjang yang dituju');
            }

            if (! Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('grade_level')
                      ->comment('Tanggal lahir santri');
            }

            if (! Schema::hasColumn('users', 'birth_place')) {
                $table->string('birth_place', 100)->nullable()->after('birth_date')
                      ->comment('Tempat lahir santri');
            }

            if (! Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['L', 'P'])->nullable()->after('birth_place')
                      ->comment('L = Laki-laki, P = Perempuan');
            }

            if (! Schema::hasColumn('users', 'nisn')) {
                $table->string('nisn', 20)->nullable()->unique()->after('gender')
                      ->comment('NISN (Nomor Induk Siswa Nasional)');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('nisn')
                      ->comment('No HP / WA santri (opsional)');
            }

            if (! Schema::hasColumn('users', 'mother_name')) {
                $table->string('mother_name')->nullable()->after('phone')
                      ->comment('Nama ibu kandung');
            }

            if (! Schema::hasColumn('users', 'father_name')) {
                $table->string('father_name')->nullable()->after('mother_name')
                      ->comment('Nama ayah');
            }

            if (! Schema::hasColumn('users', 'parent_name')) {
                $table->string('parent_name')->nullable()->after('father_name')
                      ->comment('Nama wali (opsional, jika berbeda dari ayah/ibu)');
            }

            if (! Schema::hasColumn('users', 'parent_phone')) {
                $table->string('parent_phone', 20)->nullable()->after('parent_name')
                      ->comment('No HP orang tua / wali');
            }

            if (! Schema::hasColumn('users', 'school_origin')) {
                $table->string('school_origin')->nullable()->after('parent_phone')
                      ->comment('Asal sekolah sebelumnya');
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('school_origin')
                      ->comment('Alamat lengkap domisili');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};