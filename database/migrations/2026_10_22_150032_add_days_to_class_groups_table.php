<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            // Menyimpan hari dalam bentuk JSON (contoh: ["Senin", "Kamis"])
            $table->json('days')->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table) {
            $table->dropColumn('days');
        });
    }
};