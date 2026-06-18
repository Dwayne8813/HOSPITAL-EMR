<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imaging_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imaging_order_id')->constrained()->cascadeOnDelete();
            $table->text('findings');
            $table->text('impression');
            $table->json('image_paths_json')->nullable();
            $table->foreignId('report_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('report_date')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('imaging_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_results');
    }
};
