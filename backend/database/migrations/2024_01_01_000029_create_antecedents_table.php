<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antecedents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();
            $table->text('personal')->nullable();
            $table->text('family')->nullable();
            $table->text('pathological')->nullable();
            $table->text('surgical')->nullable();
            $table->text('traumatic')->nullable();
            $table->text('allergic')->nullable();
            $table->text('pharmacological')->nullable();
            $table->timestamps();

            $table->index('medical_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antecedents');
    }
};
