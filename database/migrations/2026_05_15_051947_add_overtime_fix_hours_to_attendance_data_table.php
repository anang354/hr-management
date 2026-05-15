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
        Schema::table('attendance_data', function (Blueprint $table) {
            $table->float('overtime_fix_hours')->default(0)->after('working_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_data', function (Blueprint $table) {
            $table->dropColumn('overtime_fix_hours');
        });
    }
};
