<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->datetime('admission_date');
            $table->date('expected_discharge_date')->nullable();
            $table->datetime('actual_discharge_date')->nullable();
            $table->text('admission_diagnosis');
            $table->text('discharge_diagnosis')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active', 'discharged', 'transferred', 'deceased'])->default('active');
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('patient_id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizations');
    }
};
