<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('authorized_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('reason');
            $table->datetime('authorized_at');
            $table->timestamp('created_at')->nullable();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_authorizations');
    }
};
