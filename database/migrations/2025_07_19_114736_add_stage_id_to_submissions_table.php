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
            // Menambahkan kolom stage_id setelah task_category_id
            $table->foreignId('stage_id')
                  ->nullable()
                  ->after('task_category_id')
                  ->constrained('stages')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropColumn('stage_id');
        });
    }
};