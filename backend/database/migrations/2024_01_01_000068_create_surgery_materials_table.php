<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('surgery_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_materials');
    }
};
