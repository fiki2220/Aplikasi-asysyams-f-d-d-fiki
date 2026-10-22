<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Jam di Tabel Kelas (Class Groups)
        Schema::table('class_groups', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('name');
            $table->time('end_time')->nullable()->after('start_time');
        });

        // 2. Tambah Waktu Check In/Out Ustad di Tabel Pertemuan (Meetings)
        Schema::table('meetings', function (Blueprint $table) {
            $table->timestamp('teacher_check_in_at')->nullable()->after('date');
            $table->timestamp('teacher_check_out_at')->nullable()->after('teacher_check_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['teacher_check_in_at', 'teacher_check_out_at']);
        });
    }
};