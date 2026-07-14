<?php

use App\Models\Order;
use App\OrderStatus;
use App\PaymentMethod;
use App\PaymentStatus;
use Database\Seeders\OrderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can load factory', function () {
    $this->seed([OrderSeeder::class]);

    $order = Order::first();

    expect($order)->not()->toBeNull();
});

describe('list order', function () {
    it('can get list orders', function () {
        $this->seed([OrderSeeder::class]);
        
        $orders = Order::all();
        
        expect($orders)->not()->toBeNull();
        expect($orders->count())->toBe(15);
    });

    it('can get user in order', function() {
        $this->seed([OrderSeeder::class]);

        $order = Order::first();

        expect($order)->not()->toBeNull();

        $user = $order->user;

        expect($user)->not()->toBeNull();
        expect($user->name)->not()->toBeNull();
    });

    it('can get price in order', function () {
        $this->seed([OrderSeeder::class]);

        $order = Order::first();

        expect($order)->not()->toBeNull();
        expect($order->total)->toBeNumeric();
        expect($order->subtotal)->toBeNumeric();
        expect($order->shipping_cost)->toBeNumeric();
    });

    it('can get product in order', function () {
        $this->seed([OrderSeeder::class]);

        $order = Order::first();

        $products = $order->items;

        expect($products)->not()->toBeNull();
        expect($products->count())->toBeBetween(1, 2);

        $product = $products->first()->product;

        expect($product)->not()->toBeNull();
        expect($product->name)->toBeString();
    });

    it('can get status order', function () {
        $this->seed([OrderSeeder::class]);

        $order = Order::first();

        expect($order)->not()->toBeNull();
        expect($order->status)->toBeInstanceOf(OrderStatus::class);
        expect($order->status)->toBe(OrderStatus::Pending);
        expect($order->payment_method)->toBeInstanceOf(PaymentMethod::class);
        expect($order->payment_status)->toBeInstanceOf(PaymentStatus::class);
    });
});

describe('edit order', function () {
    it('can cancel order', function () {
        $this->seed([OrderSeeder::class]);

        $order = Order::first();
        
        expect($order)->not()->toBeNull();

        $order['status'] = OrderStatus::Canceled;
        $order->save();
        $order->refresh();

        expect($order->status)->toBe(OrderStatus::Canceled);
    });
});
