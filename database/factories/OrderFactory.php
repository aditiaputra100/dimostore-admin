<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\ShippingZone;
use App\Models\User;
use App\OrderStatus;
use App\PaymentMethod;
use App\PaymentStatus;
use Database\Seeders\ShippingZoneSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => fake()->unique()->randomNumber(6, true),
            'status' => OrderStatus::Pending,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'payment_status' => PaymentStatus::Unpaid,
            'shipping_zone_id' => ShippingZone::inRandomOrder()->first()->id,
            'subtotal' => 0,
            'shipping_cost' => 0,
            'total' => 0,
            'recipient_name' => function (array $attributes) {
                return User::find($attributes['user_id'])->addresses()->first()->recipient_name;
            },
            'recipient_phone' => function (array $attributes) {
                return User::find($attributes['user_id'])->addresses()->first()->phone;
            },
            'shipping_address' => function (array $attributes) {
                $address = User::find($attributes['user_id'])->addresses->first();
                return $address->getAddress();
            },
            'tracking_number' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
