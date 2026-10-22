<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Dokumen persyaratan PPDB & field tambahan.
 * ------------------------------------------------------------
 * File: 2026_10_09_230041_add_document_fields_to_users_table.php
 *
 * Kolom yang ditambahkan:
 *   • file_kk             → path file Kartu Keluarga
 *   • file_akta           → path file Akta Kelahiran
 *   • file_ijazah         → path file Ijazah / SKL
 *   • file_foto           → path file Pas Foto 3x4
 *   • program             → program yang didaftarkan (Tahsin/Tilawah/dll)
 *   • terms_accepted_at   → timestamp persetujuan syarat & ketentuan
 * ------------------------------------------------------------
 */
return new class extends Migration
{
    private array $columns = [
        'file_kk',
        'file_akta',
        'file_ijazah',
        'file_foto',
        'program',
        'terms_accepted_at',
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'file_kk')) {
                $table->string('file_kk')->nullable()->after('address')
                      ->comment('Path file Kartu Keluarga (KK)');
            }

            if (! Schema::hasColumn('users', 'file_akta')) {
                $table->string('file_akta')->nullable()->after('file_kk')
                      ->comment('Path file Akta Kelahiran');
            }

            if (! Schema::hasColumn('users', 'file_ijazah')) {
                $table->string('file_ijazah')->nullable()->after('file_akta')
                      ->comment('Path file Ijazah / SKL');
            }

            if (! Schema::hasColumn('users', 'file_foto')) {
                $table->string('file_foto')->nullable()->after('file_ijazah')
                      ->comment('Path file Pas Foto 3x4');
            }

            if (! Schema::hasColumn('users', 'program')) {
                $table->string('program', 50)->nullable()->after('file_foto')
                      ->comment('Program yang didaftarkan (Tahsin/Tilawah/Murottal/Baca_Tulis)');
            }

            if (! Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('program')
                      ->comment('Waktu user menyetujui syarat & ketentuan');
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