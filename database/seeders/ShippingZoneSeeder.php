<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingZone::query()->create([
            'name' => 'Zona Jawa Timur',
            'province' => 'Jawa Timur'
        ]);

        ShippingZone::query()->create([
            'name' => 'Zona Jawa Tengah',
            'province' => 'Jawa Tengah'
        ]);

        ShippingZone::query()->create([
            'name' => 'Zona Jawa Barat',
            'province' => 'Jawa Barat'
        ]);
    }
}
