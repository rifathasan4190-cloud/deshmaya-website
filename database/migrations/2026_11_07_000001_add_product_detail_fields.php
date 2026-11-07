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
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 100)->nullable()->unique();
            $table->json('packaging_options')->nullable();
            $table->json('season_months')->nullable();
            $table->string('supplier_guide_title', 180)->nullable();
            $table->string('supplier_guide_url', 2048)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn([
                'sku',
                'packaging_options',
                'season_months',
                'supplier_guide_title',
                'supplier_guide_url',
            ]);
        });
    }
};
