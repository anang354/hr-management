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
        Schema::create('attendance_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('attendance_users')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('attendance_shifts');
            $table->date('date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->integer('coming_late')->default(0); // in minutes
            $table->integer('early_leave')->default(0); // in minutes
            $table->float('overtime_hours')->default(0);
            $table->float('working_hours')->default(0);
            $table->string('status', 25)->nullable(); // Hadir, Alfa, Izin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_data');
    }
};
