<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingRate::query()->create([
            'shipping_zone_id' => 1,
            'min_weight' => 0,
            'max_weight' => 10,
            'price' => 4500,
        ]);

        ShippingRate::query()->create([
            'shipping_zone_id' => 2,
            'min_weight' => 0,
            'max_weight' => 10,
            'price' => 5000,
        ]);

        ShippingRate::query()->create([
            'shipping_zone_id' => 3,
            'min_weight' => 0,
            'max_weight' => 10,
            'price' => 5500,
        ]);
    }
}
