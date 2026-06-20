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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id', unsigned:true);
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'canceled']);
            $table->enum('payment_method', ['bank_transfer', 'qris', 'cod']);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded']);
            $table->decimal('subtotal', 15);
            $table->decimal('shipping_cost', 10);
            $table->decimal('total', 15);
            $table->bigInteger('shipping_zone_id', unsigned:true)->nullable();
            $table->string('recipient_name', 100);
            $table->string('recipient_phoe', 20);
            $table->text('shipping_address');
            $table->string('tracking_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('RESTRICT');
            $table->foreign('shipping_zone_id')->references('id')->on('shipping_zones')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->dropForeign(['user_id', 'shipping_zone_id']);
        });

        Schema::dropIfExists('orders');
    }
};
