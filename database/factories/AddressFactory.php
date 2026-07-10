<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;
    protected static ?string $recipientName;
    protected static ?string $phone;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $provinces = ['Jawa Timur', 'Jawa Tengah', 'Jawa Barat'];

        return [
            'user_id' => User::factory(),
            'recipient_name' => static::$recipientName ??= fake()->name(),
            'phone' => static::$phone ??= fake()->phoneNumber(),
            'address_line' => fake()->address(),
            'city' => fake()->city(),
            'province' => fake()->randomElement($provinces),
            'postal_code' => fake()->postcode(),
            'is_default' => 0,
        ];
    }

    public function isDefault(): Factory {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => 1,
            ];
        });
    }
}
