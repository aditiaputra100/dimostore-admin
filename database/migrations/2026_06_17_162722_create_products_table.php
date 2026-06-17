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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->longText('description')->nullable();
            $table->string('sku', 50)->unique();
            $table->decimal('price', 15);
            $table->decimal('original_price', 15)->nullable();
            $table->integer('stock', unsigned:true)->default(0);
            $table->integer('weight', unsigned:true);
            $table->string('main_image')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive']);
            $table->integer('sold_count', unsigned:true)->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
