<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triage_id')->constrained('emergency_triage')->cascadeOnDelete();
            $table->json('vital_signs_json')->nullable();
            $table->text('treatment_given')->nullable();
            $table->string('disposition')->nullable();
            $table->datetime('disposition_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('triage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_records');
    }
};
