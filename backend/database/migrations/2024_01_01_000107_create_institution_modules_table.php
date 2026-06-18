<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('module_name');
            $table->boolean('is_enabled')->default(true);
            $table->datetime('configured_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['institution_id', 'module_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_modules');
    }
};
