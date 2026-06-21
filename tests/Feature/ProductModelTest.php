<?php

use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

test('Create product test', function () {
    $this->seed([CategorySeeder::class, ProductSeeder::class]);

    assertDatabaseCount('products', 10);

    $products = Product::all();

    expect($products->count())->toBe(10);
});

test('Get category product test', function() {
    $this->seed([CategorySeeder::class, ProductSeeder::class]);

    $product = Product::query()->first();

    expect($product)->not()->toBeNull();
    expect($product->status)->toBeIn(['draft', 'active', 'inactive']);

    $category = $product->category;

    expect($category)->not()->toBeNull();
    expect($category->name)->toBeString();
});

