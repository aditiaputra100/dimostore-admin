<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name' => function (array $attributes) {
                return Product::find($attributes['product_id'])->name;
            },
            'price' => function (array $attributes) {
                return Product::find($attributes['product_id'])->price;
            },
            'quantity' => fake()->numberBetween(1, 5),
            'subtotal' => function (array $attributes) {
                return $attributes['price'] * $attributes['quantity'];
            },
        ];
    }
}
