<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('study_type');
            $table->string('template_name');
            $table->text('template_content');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->index(['institution_id', 'study_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_templates');
    }
};
