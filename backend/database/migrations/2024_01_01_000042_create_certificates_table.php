<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('certificate_type', ['medical', 'rest', 'work', 'school']);
            $table->string('title');
            $table->text('content');
            $table->text('diagnosis')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('pdf_path')->nullable();
            $table->text('digital_signature')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['institution_id', 'certificate_type']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
