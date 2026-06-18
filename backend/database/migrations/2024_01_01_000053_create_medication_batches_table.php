<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->unsignedInteger('quantity');
            $table->date('expiry_date');
            $table->decimal('purchase_price', 10, 2);
            $table->enum('status', ['active', 'expired', 'depleted'])->default('active');
            $table->timestamp('created_at')->nullable();

            $table->index(['medication_id', 'status']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_batches');
    }
};
