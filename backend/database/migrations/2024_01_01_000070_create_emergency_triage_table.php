<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_triage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('arrival_date');
            $table->text('chief_complaint');
            $table->enum('triage_level', ['I', 'II', 'III', 'IV', 'V']);
            $table->unsignedTinyInteger('triage_score');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['waiting', 'in_treatment', 'admitted', 'discharged', 'referred'])->default('waiting');
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('patient_id');
            $table->index('triage_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_triage');
    }
};
