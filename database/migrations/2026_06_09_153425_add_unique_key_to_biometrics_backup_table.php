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
        Schema::table('biometric_backups', function (Blueprint $table) {
            //
            $table->unique(['biometric_id', 'machine_sn', 'finger_index'], 'unique_biometric_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometrics_backup', function (Blueprint $table) {
            //
            $table->dropUnique('unique_biometric_record');
        });
    }
};
