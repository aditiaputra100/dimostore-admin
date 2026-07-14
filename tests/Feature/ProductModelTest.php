<?php

use App\Models\Product;
use App\Models\ProductImage;
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

test('Create product with images test', function() {
    $this->seed([CategorySeeder::class, ProductSeeder::class]);

    $product = Product::query()->first();

    expect($product)->not()->toBeNull();

    $productImage1 = new ProductImage([
        'image_path' => 'some/path',
    ]);
    $productImage2 = new ProductImage([
        'image_path' => 'some/another',
    ]);

    $product->images()->saveMany([$productImage1, $productImage2]);

    $images = $product->images;

    expect($images)->not()->toBeNull();
    expect($images->count())->toBe(2);


});