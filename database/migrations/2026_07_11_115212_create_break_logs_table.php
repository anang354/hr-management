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
        Schema::create('break_logs', function (Blueprint $table) {
            $table->id();
            $table->string('biometric_id', 10)->index();
            $table->string('machine_sn', 25)->nullable();
            $table->timestamp('attendance_time')->nullable();
            $table->integer('type')->nullable();
            $table->integer('verify_method')->nullable();
            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('break_logs');
    }
};
