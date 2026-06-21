<?php

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

test('Create category test', function () {
    $this->seed([CategorySeeder::class]);

    assertDatabaseCount('categories', 6);
});

test('Get categories test', function() {
    $this->seed([CategorySeeder::class]);

    $categories = Category::whereNull('parent_id')->get();
    
    expect($categories->count())->toBe(3);
    expect($categories[0]->name)->toBeString()->toBe('Elektronik');
    expect($categories[0]->slug)->toBeString()->toBe('elektronik');
    expect($categories[0]->description)->toBeString();
    expect($categories[0]->image)->toBeNull();
    expect($categories[0]->is_active)->toBeOne();
});

test('Get sub categories test', function() {
    $this->seed([CategorySeeder::class]);

    $electronic = Category::query()->find(1);

    expect($electronic)->not()->toBeNull();

    $subCategory = $electronic->children()->get();

    expect($subCategory->count())->toBe(1);
    expect($subCategory[0]->name)->toBe('Komputer dan Laptop');
});

test('Get parent categoriy test', function() {
    $this->seed([CategorySeeder::class]);

    $computerAndLaptop = Category::query()->where('name', 'Komputer dan Laptop')->first();

    expect($computerAndLaptop)->not()->toBeNull();

    $parent = $computerAndLaptop->parent;

    expect($parent->name)->toBeString()->toBe('Elektronik');
});