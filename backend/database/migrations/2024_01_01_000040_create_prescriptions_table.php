<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('prescription_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'dispensed', 'cancelled'])->default('draft');
            $table->text('digital_signature')->nullable();
            $table->text('qr_code')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('medical_record_id');
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
