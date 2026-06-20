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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id', unsigned:true);
            $table->bigInteger('product_id', unsigned:true);
            $table->string('product_name', 200);
            $table->decimal('price', 15);
            $table->integer('quantity', unsigned:true);
            $table->decimal('subtotal', 15);
        });

        Schema::table('order_items', function(Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('CASCADE');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('RESTRICT');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function(Blueprint $table) {
            $table->dropForeign(['order_id', 'product_id']);

        });

        Schema::dropIfExists('order_items');
    }
};
