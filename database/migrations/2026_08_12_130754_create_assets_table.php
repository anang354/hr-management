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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_item_id')->constrained('asset_items')->onDelete('cascade');
            $table->foreignId('asset_location_id')->constrained('asset_locations')->onDelete('cascade');
            $table->string('asset_code')->unique();
            $table->json('specifications')->nullable();
            $table->string('status')->default('active');
            $table->string('condition')->default('good');
            $table->string('user')->nullable();
            $table->string('ip_address')->nullable();
            $table->date('date_of_entry')->nullable();
            $table->date('damage_date')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
