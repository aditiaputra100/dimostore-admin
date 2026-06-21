<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $laptop = new Product();
        // $laptop['category_id'] = 1; # Electronic
        // $laptop['name'] = 'MSI CYBORG 15 CORE 5 210H RTX5060';
        
        // $lower = mb_strtolower($laptop['name']);
        // $slug = preg_replace('/[^a-z0-9\s-]/', '', $lower);
        // $slug = preg_replace('/[\s-]+/', '-', $slug);

        // $laptop['slug'] = trim($slug, '-');
        // $laptop['sku'] = 'MSI-15-5';
        // $laptop['price'] = 18799550;
        // $laptop['original_price'] = 12000000;
        // $laptop['stock'] = 10;
        // $laptop['weight'] = 8;
        // $laptop['status'] = 'draft';

        // $laptop->save();

        Product::factory(10)->create();

    }
}
