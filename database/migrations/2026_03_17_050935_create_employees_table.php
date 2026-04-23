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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('employee_pos_id')->constrained('employee_pos')->cascadeOnDelete();
            $table->string('employee_number')->nullable();
            $table->string('nik');
            $table->string('name');
            $table->string('job')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('residential_address');
            $table->string('place_of_birth');
            $table->date('birth_date');
            $table->date('join_date');
            $table->string('religion');
            $table->string('mothers_name')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('photo')->nullable();
            $table->string('last_education')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('npwp')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bpjs_ketenagakerjaan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
