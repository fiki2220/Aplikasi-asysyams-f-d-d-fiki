<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_assessments', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_group_id')->nullable()->constrained('class_groups')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->foreignId('assessor_id')->nullable()->constrained('users')->onDelete('set null');

            // 5 Aspek Karakter (0-100)
            $table->unsignedTinyInteger('adab_islam')->default(0);
            $table->unsignedTinyInteger('kerjasama')->default(0);
            $table->unsignedTinyInteger('tanggung_jawab')->default(0);
            $table->unsignedTinyInteger('kedisiplinan')->default(0);
            $table->unsignedTinyInteger('kejujuran')->default(0);

            // Catatan
            $table->text('notes')->nullable();

            $table->timestamps();

            // Unique: 1 user hanya boleh punya 1 penilaian per kelas + semester
            $table->unique(['user_id', 'class_group_id', 'semester_id'], 'unique_character_per_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_assessments');
    }
};