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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cart_id', unsigned:true);
            $table->bigInteger('product_id', unsigned:true);
            $table->integer('quantity', unsigned:true)->default(1);
            $table->decimal('price', 15);
        });

        Schema::table('cart_items', function(Blueprint $table) {
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('CASCADE');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function(Blueprint $table) {
            $table->dropForeign(['cart_id', 'product_id']);
        });

        Schema::dropIfExists('cart_items');
    }
};
