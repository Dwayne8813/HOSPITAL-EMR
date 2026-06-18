<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'insurance', 'mixed']);
            $table->string('reference_number')->nullable();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->datetime('received_at');
            $table->text('notes')->nullable();
            $table->enum('status', ['completed', 'refunded', 'pending'])->default('completed');
            $table->timestamp('created_at')->nullable();

            $table->index(['invoice_id', 'status']);
            $table->index(['institution_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
