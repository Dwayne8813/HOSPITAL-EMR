<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('specialty_id')->nullable()->constrained()->nullOnDelete();
            $table->json('template_json')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_public')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->index(['institution_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_templates');
    }
};
