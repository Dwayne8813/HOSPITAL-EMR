<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('internal_code')->nullable();
            $table->string('medical_record_number')->nullable();
            $table->string('ci')->nullable();
            $table->string('passport')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_last_name')->nullable();
            $table->date('birth_date');
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['M', 'F', 'O']);
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed', 'cohabiting'])->default('single');
            $table->string('nationality')->default('Boliviana');
            $table->text('address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('insurance_company')->nullable();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->enum('status', ['active', 'inactive', 'deceased'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'ci']);
            $table->index(['institution_id', 'last_name', 'first_name']);
            $table->index('institution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
