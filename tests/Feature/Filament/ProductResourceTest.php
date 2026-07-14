<?php

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(RefreshDatabase::class);

describe('list products', function() {
    it('can load page', function() {
        $this->seed([CategorySeeder::class]);

        $products = Product::factory()->count(5)->create();

        Livewire::test(ListProducts::class)
            ->assertOk()
            ->assertCanSeeTableRecords($products)
            ->assertCanRenderTableColumn('main_image')
            ->assertCanRenderTableColumn('name')
            ->assertCanRenderTableColumn('sku')
            ->assertCanRenderTableColumn('price')
            ->assertCanRenderTableColumn('stock')
            ->assertCanRenderTableColumn('status');
    });
});

describe('create product', function() {
    it('can load page', function() {
        Livewire::test(CreateProduct::class)
            ->assertOk();
    });

    it('can create product', function() {
        $this->seed([CategorySeeder::class]);

        $product = Product::factory()->make();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
                'weight' => $product->weight,
                'status' => $product->status,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();
        
        assertDatabaseHas(Product::class, [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'sku' => $product->sku,
            'price' => $product->price,
            'original_price' => $product->original_price,
            'stock' => $product->stock,
            'weight' => $product->weight,
            'status' => $product->status,
        ]);
    });

    it('can create product with image', function() {
        $this->seed([CategorySeeder::class]);

        Storage::fake('public');

        $product = Product::factory()->make();
        $image1 = UploadedFile::fake()->image('1.jpg');
        $image2 = UploadedFile::fake()->image('2.jpg');

        $product['main_image'] = $image1;

        $productImages = new ProductImage([
            'product_id' => $product->id,
            'image_path' => $image2
        ]);

        $product->setRelation('images', collect([$productImages]));

        expect($product->main_image)->not()->toBeNull();
        expect($product->images)->not()->toBeNull();
        expect($product->images->count())->toBeOne();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'category_id' => $product->category_id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
                'weight' => $product->weight,
                'status' => $product->status,
                'main_image' => $product->main_image,
                'images' => [$product->images],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        expect(Product::count())->toBeOne();

        $product = Product::query()->first();

        assertDatabaseHas(Product::class, [
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'sku' => $product->sku,
            'price' => $product->price,
            'original_price' => $product->original_price,
            'stock' => $product->stock,
            'weight' => $product->weight,
            'status' => $product->status,
            'main_image' => $product->main_image,
        ]);

        Storage::disk('public')->assertExists($product->main_image);
        expect($product->images)->toHaveCount(1);

        assertDatabaseHas(ProductImage::class, [
            'product_id' => $product->id,
            'image_path' => $product->images[0]->image_path,
            'sort_order' => 0,
        ]);

    });
});

describe('edit product', function() {
    it('can load page', function() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->first();

        Livewire::test(EditProduct::class, [
            'record' => $product->id
        ])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'description' => $product->description,
                'price' => $product->price,
                'original_price' => $product->original_price,
            ]);
    });

    it('can update product', function() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->first();
        $product['name'] = 'testing';
        $product['description'] = 'deskripsi update';
        $product['price'] = 330;
        $product['original_price'] = 200;
        $product['stock'] = 99;

        Livewire::test(EditProduct::class, [
            'record' => $product->id,
        ])
            ->fillForm([
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();
        
        assertDatabaseHas(Product::class, [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'original_price' => $product->original_price,
            'stock' => $product->stock,
        ]);
    });

    it('validate form data', function(array $data, array $errors) {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->first();

        $product['name'] = $data['name'];
        $product['description'] = $data['description'];
        $product['sku'] = $data['sku'];
        $product['price'] = $data['price'];
        $product['original_price'] = $data['original_price'];
        $product['stock'] = $data['stock'];

        Livewire::test(EditProduct::class, [
            'record' => $product->id,
        ])
            ->fillForm([
                'name' => $product->name,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
            ])
            ->call('save')
            ->assertHasFormErrors($errors)
            ->assertNotNotified();
    })->with([
        '`name` is required' => [
            [
                'name' => null,
                'description' => fake()->paragraph(2),
                'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
                'price' => fake()->randomFloat(min: 10, max:500),
                'original_price' => fake()->randomFloat(min: 0, max:500),
                'stock' => fake()->numberBetween(1, 10),
            ],
            ['name' => 'required']
        ],
        '`name` is max 200 character' => [
            [
                'name' => Str::random((201)),
                'description' => fake()->paragraph(2),
                'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
                'price' => fake()->randomFloat(min: 10, max:500),
                'original_price' => fake()->randomFloat(min: 0, max:500),
                'stock' => fake()->numberBetween(1, 10),
            ],
            ['name' => 'max']
        ],
        '`sku` is required' => [
            [
                'name' => fake()->words(3, true),
                'description' => fake()->paragraph(2),
                'sku' => null,
                'price' => fake()->randomFloat(min: 10, max:500),
                'original_price' => fake()->randomFloat(min: 0, max:500),
                'stock' => fake()->numberBetween(1, 10),
            ],
            ['sku' => 'required']
        ],
        '`sku` is max 50 character' => [
            [
                'name' => fake()->words(3, true),
                'description' => fake()->paragraph(2),
                'sku' => Str::random(51),
                'price' => fake()->randomFloat(min: 10, max:500),
                'original_price' => fake()->randomFloat(min: 0, max:500),
                'stock' => fake()->numberBetween(1, 10),   
            ],
            ['sku' => 'max']
        ],
    ]);
});

describe('delete product', function() {
    it('can delete product in edit page', function() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->first();

        Livewire::test(EditProduct::class, [
            'record' => $product->id,
        ])
            ->assertOk()
            ->callAction(DeleteAction::class)
            ->assertNotified()
            ->assertRedirect();

        assertSoftDeleted($product);
    });

    it('can delete product in list page and restore it', function() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->first();

        Livewire::test(ListProducts::class)
            ->callAction(TestAction::make('delete')->table($product));
        
        assertSoftDeleted($product);

        Livewire::test(ListProducts::class)
            ->filterTable('trashed', 'only')
            ->callAction(TestAction::make('restore')->table($product));

        $product->refresh();

        expect($product->deleted_at)->toBeNull();
    });
});