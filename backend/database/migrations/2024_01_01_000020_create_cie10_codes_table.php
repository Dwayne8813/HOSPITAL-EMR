<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cie10_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('cie10_categories')->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('age_range')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->index('code');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cie10_codes');
    }
};
