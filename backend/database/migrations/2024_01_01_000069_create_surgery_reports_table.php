<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_schedule_id')->constrained()->cascadeOnDelete();
            $table->datetime('report_date');
            $table->text('findings');
            $table->text('complications')->nullable();
            $table->text('outcome');
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index('surgery_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_reports');
    }
};
