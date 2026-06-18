<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_specimens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->string('specimen_type');
            $table->datetime('collected_at');
            $table->foreignId('collected_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['collected', 'processing', 'analyzed'])->default('collected');
            $table->timestamp('created_at')->nullable();

            $table->index('lab_order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_specimens');
    }
};
