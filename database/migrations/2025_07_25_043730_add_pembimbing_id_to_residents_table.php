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
        Schema::table('residents', function (Blueprint $table) {
            // Menambahkan foreign key ke tabel users, bisa null jika belum ada pembimbing
            $table->foreignId('id_pembimbing')->nullable()->after('start_date')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['id_pembimbing']);
            $table->dropColumn('id_pembimbing');
        });
    }
};
