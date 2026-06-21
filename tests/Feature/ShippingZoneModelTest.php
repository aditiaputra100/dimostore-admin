<?php

use App\Models\ShippingZone;
use Database\Seeders\ShippingRateSeeder;
use Database\Seeders\ShippingZoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

test('Create shipping zone test', function () {
    $this->seed([ShippingZoneSeeder::class]);

    assertDatabaseCount('shipping_zones', 3);
});


test('Get shipping zone test', function() {
    $this->seed([ShippingZoneSeeder::class]);

    $eastJava = ShippingZone::query()->where('name', 'Zona Jawa Timur')->first();

    expect($eastJava)->not()->toBeNull();
    expect($eastJava->province)->toBe('Jawa Timur');
});

test('Get price shipping test', function() {
    $this->seed([ShippingZoneSeeder::class, ShippingRateSeeder::class]);

    $eastJava = ShippingZone::query()->where('name', 'Zona Jawa Timur')->first();

    expect($eastJava)->not()->toBeNull();
    $rate_pricing = $eastJava->shippingRates()->first();

    expect($rate_pricing)->not()->toBeNull();
    expect($rate_pricing->price)->toBe(4500);
});