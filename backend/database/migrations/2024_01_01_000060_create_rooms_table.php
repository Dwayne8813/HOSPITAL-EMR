<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('name')->nullable();
            $table->enum('room_type', ['individual', 'shared', 'ward', 'icu', 'emergency', 'surgery', 'recovery']);
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'cleaning'])->default('available');
            $table->timestamps();

            $table->index(['institution_id', 'status']);
            $table->index('floor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
