<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
#[UseModel(Product::class)]
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(min: 10, max:500);
        $originalPrice = fake()->randomFloat(min: 0, max:500);
        $statusArray = ['draft', 'active', 'inactive'];
        $randomStatusKey = array_rand($statusArray);

        
        while ($originalPrice > $price) {
            $originalPrice = fake()->randomFloat(min: 0, max:500);
        }

        return [
            'category_id' => Category::query()->inRandomOrder(CategorySeeder::class)->first()?->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(2),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
            'price' => $price,
            'original_price' => $originalPrice,
            'stock' => fake()->numberBetween(1, 10),
            'weight' => fake()->numberBetween(5, 20),
            'status' => $statusArray[$randomStatusKey],
        ];
    }
}
