<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete()->index();
            $table->date('date')->index();
            $table->string('shift')->nullable(); // Day/Night
            $table->time('checkin')->nullable();
            $table->time('checkout')->nullable();
            $table->time('breakout')->nullable();
            $table->time('breakin')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            // Pastikan satu karyawan hanya punya satu record per hari
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
