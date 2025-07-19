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
        Schema::create('division_staff', function (Blueprint $table) {
            // Kunci relasi adalah 'user_id', bukan 'lecturer_id'
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            
            // Tambahan jika diperlukan
            $table->boolean('is_pj')->default(false); // Penanggung Jawab
            $table->timestamps();

            // Primary key gabungan
            $table->primary(['user_id', 'division_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_staff');
    }
};