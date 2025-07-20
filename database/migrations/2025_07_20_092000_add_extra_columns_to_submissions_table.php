<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Menambahkan kolom-kolom yang hilang dari tabel 'ilmiah' lama
            $table->text('description')->nullable()->after('title');
            $table->text('seminar_title')->nullable()->after('description');
            $table->text('presentation_file_path')->nullable()->after('file_path');
            $table->text('grade_file_path')->nullable()->after('grade');
            $table->text('attendance_file_path')->nullable()->after('grade_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'seminar_title',
                'presentation_file_path',
                'grade_file_path',
                'attendance_file_path',
            ]);
        });
    }
};
