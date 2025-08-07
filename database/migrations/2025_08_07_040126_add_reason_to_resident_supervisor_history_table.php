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
        Schema::table('resident_supervisor_history', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan alasan pergantian
            $table->text('reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_supervisor_history', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
