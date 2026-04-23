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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('sequence_number')->unsigned();
            $table->string('contract_number')->unique();
            $table->integer('gaji_pokok');
            $table->integer('tunjangan_jabatan');
            $table->integer('tunjangan_bahasa')->default(0);
            $table->integer('tunjangan_keahlian')->default(0);
            $table->integer('tunjangan_transportasi')->default(0);
            $table->integer('tunjangan_lainnya')->default(0);
            $table->integer('total_gaji');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('contract_periode');
            $table->string('contract_type', 50);
            $table->json('snapshot_metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
