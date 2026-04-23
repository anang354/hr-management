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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('exit_date')->nullable()->after('join_date');
            $table->boolean('is_active')->default(1);
            $table->string('exit_reason')->nullable()->after('exit_date');
            $table->string('ptkp_status')->nullable()->after('bpjs_ketenagakerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('exit_date');
            $table->dropColumn('is_active');
            $table->dropColumn('exit_reason');
            $table->dropColumn('ptkp_status');
        });
    }
};
