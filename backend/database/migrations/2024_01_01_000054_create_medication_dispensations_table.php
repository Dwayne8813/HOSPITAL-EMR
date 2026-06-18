<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('medication_batches')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->foreignId('dispensed_by')->constrained('users')->cascadeOnDelete();
            $table->datetime('dispensed_at');
            $table->timestamp('created_at')->nullable();

            $table->index('patient_id');
            $table->index('medication_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_dispensations');
    }
};
