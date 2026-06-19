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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id', unsigned:true);
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('product_images', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function(Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('product_images');
    }
};
