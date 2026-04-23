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
        Schema::create('overtime_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overtime_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees'); // ID Karyawan
            $table->time('start_time'); // Jam Masuk
            $table->time('end_time');   // Jam Keluar
            $table->decimal('overtime_hours', 4, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_request_items');
    }
};
