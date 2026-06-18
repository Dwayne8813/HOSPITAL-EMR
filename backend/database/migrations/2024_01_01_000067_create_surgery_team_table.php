<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role');
            $table->datetime('assigned_at');
            $table->timestamp('created_at')->nullable();

            $table->index('surgery_schedule_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_team');
    }
};
