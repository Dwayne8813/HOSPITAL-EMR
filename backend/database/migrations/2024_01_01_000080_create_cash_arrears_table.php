<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_arrears', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained()->cascadeOnDelete();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->decimal('expected_amount', 12, 2);
            $table->decimal('actual_amount', 12, 2)->nullable();
            $table->decimal('difference', 12, 2)->nullable();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('cash_register_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_arrears');
    }
};
