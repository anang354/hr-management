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
        Schema::create('biometric_backups', function (Blueprint $table) {
            $table->id();
            $table->string('biometric_id', 100); // PIN pada mesin
            $table->string('machine_sn', 25);   // Serial Number mesin asal
            $table->integer('finger_index'); // FID (0-9)
            $table->longText('template');   // Data TMP (Base64)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_backups');
    }
};
