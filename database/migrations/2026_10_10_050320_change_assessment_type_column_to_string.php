<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom assessment_type jadi VARCHAR(50)
        // agar bisa tampung "ziyadah,murojaah,tahsin,tilawah"
        DB::statement("ALTER TABLE assessments MODIFY assessment_type VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        // Balik ke VARCHAR(20) kalau di-rollback
        DB::statement("ALTER TABLE assessments MODIFY assessment_type VARCHAR(20) NOT NULL");
    }
};