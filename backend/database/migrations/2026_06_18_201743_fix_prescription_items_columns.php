<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'medication_id')) {
                $table->foreignId('medication_id')->nullable()->after('medication_name')->constrained('medications')->nullOnDelete();
            }
            if (!Schema::hasColumn('prescription_items', 'dosage_unit')) {
                $table->string('dosage_unit')->nullable()->after('dosage');
            }
            if (!Schema::hasColumn('prescription_items', 'duration_days')) {
                $table->integer('duration_days')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('prescription_items', 'refill_quantity')) {
                $table->integer('refill_quantity')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('prescription_items', 'is_prn')) {
                $table->boolean('is_prn')->default(false)->after('instructions');
            }
            if (!Schema::hasColumn('prescription_items', 'is_compound')) {
                $table->boolean('is_compound')->default(false)->after('is_prn');
            }
            if (!Schema::hasColumn('prescription_items', 'compound_recipe')) {
                $table->json('compound_recipe')->nullable()->after('is_compound');
            }
            if (!Schema::hasColumn('prescription_items', 'status')) {
                $table->string('status')->default('active')->after('compound_recipe');
            }
            if (!Schema::hasColumn('prescription_items', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn(['medication_id', 'dosage_unit', 'duration_days', 'refill_quantity', 'is_prn', 'is_compound', 'compound_recipe', 'status', 'updated_at']);
        });
    }
};
