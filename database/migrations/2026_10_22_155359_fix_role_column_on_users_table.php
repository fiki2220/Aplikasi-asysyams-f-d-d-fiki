<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // ✅ Ubah menjadi VARCHAR agar fleksibel menerima role apa pun (termasuk 'admin')
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL DEFAULT 'student'");
        } elseif ($driver === 'sqlite') {
             // Jika Anda menggunakan SQLite (biasanya saat testing)
             DB::statement("ALTER TABLE users MODIFY role VARCHAR(50) NOT NULL DEFAULT 'student'");
        }
    }

    public function down(): void
    {
        // Opsional: Kembalikan ke ENUM lama jika migrasi di-rollback
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin', 'guru', 'student') NOT NULL DEFAULT 'student'");
        }
    }
};