<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_id')->nullable()->constrained('medication_catalog')->nullOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('presentation')->nullable();
            $table->string('concentration')->nullable();
            $table->string('laboratory')->nullable();
            $table->boolean('requires_prescription')->default(true);
            $table->unsignedInteger('min_stock')->default(0);
            $table->unsignedInteger('current_stock')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
