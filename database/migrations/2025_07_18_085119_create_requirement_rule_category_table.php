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
        Schema::create('requirement_rule_category', function (Blueprint $table) {
            $table->foreignId('requirement_rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_category_id')->constrained()->cascadeOnDelete();

            $table->primary(['requirement_rule_id', 'task_category_id'], 'rule_category_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_rule_category');
    }
};