<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createCategory();
        $this->createSubCategory();

    }

    public function createCategory() {
        $electronic = new Category();
        $electronic['name'] = 'Elektronik';
        $electronic['slug'] = 'elektronik';
        $electronic['description'] = 'Deskripsi dari kategori elektronik';
        $electronic->save();

        $dailyNeeds = new Category();
        $dailyNeeds['name'] = 'Kebutuhan Harian';
        $dailyNeeds['slug'] = 'kebutuhan-harian';
        $dailyNeeds['description'] = 'Deskripsi dari kategori kebutuhan harian';
        $dailyNeeds->save();

        $homeFurnishings = new Category();
        $homeFurnishings['name'] = 'Perlengkapan Rumah Tangga';
        $homeFurnishings['slug'] = 'perlengkapan-rumah-tangga';
        $homeFurnishings['description'] = 'Deskripsi dari kategori perlengkapan rumah tangga';
        $homeFurnishings->save();
    }

    public function createSubCategory() {
        $computerAndLaptop = new Category();
        $computerAndLaptop['name'] = 'Komputer dan Laptop';
        $computerAndLaptop['parent_id'] = 1;
        $computerAndLaptop['slug'] = 'komputer-dan-laptop';
        $computerAndLaptop['description'] = 'Deskripsi dari sub kategori komputer dan laptop';
        $computerAndLaptop->save();

        $health = new Category();
        $health['name'] = 'Kesehatan';
        $health['parent_id'] = 2;
        $health['slug'] = 'kesehatan';
        $health['description'] = 'Deskripsi dari kategori kesehatan';
        $health->save();

        $kitchen = new Category();
        $kitchen['name'] = 'Dapur';
        $kitchen['parent_id'] = 3;
        $kitchen['slug'] = 'dapur';
        $kitchen['description'] = 'Deskripsi dari kategori dapur';
        $kitchen->save();
    }
}
