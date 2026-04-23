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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('letter_number')->unique();
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 4, 1);
            $table->enum('leave_session', ['fullday', 'halfday'])->default('fullday');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending');
            $table->string('rejection_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
