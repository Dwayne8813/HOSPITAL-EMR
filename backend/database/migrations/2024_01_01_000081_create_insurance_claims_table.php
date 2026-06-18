<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('insurance_company');
            $table->string('claim_number')->nullable();
            $table->decimal('claimed_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->enum('status', ['submitted', 'processing', 'approved', 'denied', 'paid'])->default('submitted');
            $table->datetime('submitted_at')->nullable();
            $table->datetime('resolved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index('claim_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
