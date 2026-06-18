<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_a_id')->constrained('medications')->cascadeOnDelete();
            $table->foreignId('medication_b_id')->constrained('medications')->cascadeOnDelete();
            $table->enum('severity', ['mild', 'moderate', 'severe', 'contraindicated']);
            $table->text('description');
            $table->text('recommendation');
            $table->timestamp('created_at')->nullable();

            $table->index(['medication_a_id', 'medication_b_id']);
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interactions');
    }
};
