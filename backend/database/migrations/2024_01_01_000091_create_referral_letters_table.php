<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('to_institution')->nullable();
            $table->text('reason');
            $table->text('clinical_summary');
            $table->enum('urgency', ['normal', 'urgent', 'emergency'])->default('normal');
            $table->enum('status', ['sent', 'received', 'completed'])->default('sent');
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_letters');
    }
};
