<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('document_type', 50)->nullable()->after('last_name');
            $table->string('document_number', 50)->nullable()->after('document_type');
            $table->string('marital_status', 50)->nullable()->after('gender');
            $table->string('occupation', 255)->nullable()->after('address');
            $table->string('emergency_contact_relationship', 100)->nullable()->after('emergency_contact_phone');
            $table->text('notes')->nullable()->after('status');
        });

        // Migrate existing CI data to document_type/document_number
        DB::statement("UPDATE patients SET document_type = 'CI', document_number = ci WHERE ci IS NOT NULL AND ci != ''");
        DB::statement("UPDATE patients SET document_type = 'PASAPORTE', document_number = passport WHERE passport IS NOT NULL AND passport != ''");
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_number',
                'marital_status',
                'occupation',
                'emergency_contact_relationship',
                'notes',
            ]);
        });
    }
};
