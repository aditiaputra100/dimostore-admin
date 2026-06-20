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
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id', unsigned:true);
            $table->string('status', 30);
            $table->text('note')->nullable();
            $table->bigInteger('created_by', unsigned:true);
        });

        Schema::table('order_status_histories', function(Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('CASCADE');
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_histories', function(Blueprint $table) {
            $table->dropForeign(['order_id', 'created_by']);
        });

        Schema::dropIfExists('order_status_histories');
    }
};
