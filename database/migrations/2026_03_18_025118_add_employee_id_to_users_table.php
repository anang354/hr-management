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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->softDeletes();
        });
        Schema::table('overtime_request_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('overtime_hours');
            $table->string('reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropSoftDeletes();
        });
        Schema::table('overtime_request_items', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('reason');
        });
    }
};
