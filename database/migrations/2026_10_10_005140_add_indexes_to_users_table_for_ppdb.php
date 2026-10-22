<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index ke tabel users untuk mempercepat query.
     * Index ini penting untuk filter by role, status aktif, dan sorting.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Index untuk filter by role (tab Siswa, Guru, Super Admin)
            if (! $this->hasIndex('users', 'users_role_index')) {
                $table->index('role', 'users_role_index');
            }

            // Index untuk filter by is_active (tab Pending)
            if (! $this->hasIndex('users', 'users_is_active_index')) {
                $table->index('is_active', 'users_is_active_index');
            }

            // Composite index untuk kombinasi role + is_active
            if (! $this->hasIndex('users', 'users_role_is_active_index')) {
                $table->index(['role', 'is_active'], 'users_role_is_active_index');
            }

            // Index untuk sorting created_at desc
            if (! $this->hasIndex('users', 'users_created_at_index')) {
                $table->index('created_at', 'users_created_at_index');
            }
        });
    }

    /**
     * Hapus semua index yang dibuat.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->hasIndex('users', 'users_role_index')) {
                $table->dropIndex('users_role_index');
            }

            if ($this->hasIndex('users', 'users_is_active_index')) {
                $table->dropIndex('users_is_active_index');
            }

            if ($this->hasIndex('users', 'users_role_is_active_index')) {
                $table->dropIndex('users_role_is_active_index');
            }

            if ($this->hasIndex('users', 'users_created_at_index')) {
                $table->dropIndex('users_created_at_index');
            }
        });
    }

    /**
     * Helper: cek apakah index sudah ada di tabel.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);

            return ! empty($indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }
};