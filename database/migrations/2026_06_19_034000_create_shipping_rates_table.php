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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shipping_zone_id', unsigned:true);
            $table->integer('min_weight', unsigned:true);
            $table->integer('max_weight', unsigned:true);
            $table->decimal('price', 10);
        });

        Schema::table('shipping_zones', function(Blueprint $table) {
            $table->foreign('shipping_zone_id')->references('id')->on('shipping_zones')->onDelete('CASCADE');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_zones', function(Blueprint $table) {
            $table->dropForeign(['shipping_zone_id']);
        });

        Schema::dropIfExists('shipping_rates');
    }
};
