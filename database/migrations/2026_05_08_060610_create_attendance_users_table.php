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
        Schema::create('attendance_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('biometric_id', 10)->unique(); // PIN/User ID di mesin
            $table->string('display_name', 100)->nullable()->index();
            $table->integer('privilege')->default(0); // 0=User, 14=Admin
            $table->string('card_number', 25)->nullable();
            $table->string('password', 25)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_users');
    }
};
