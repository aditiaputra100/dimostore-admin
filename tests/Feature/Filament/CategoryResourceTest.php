<?php

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Filament\Resources\Categories\RelationManagers\ChildrenRelationManager;
use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;

uses(RefreshDatabase::class);

describe('list category', function() {
    it('can load page', function() {
        $this->seed([CategorySeeder::class]);

        $categories = Category::whereNull('parent_id')->get();

        Livewire::test(ListCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    });

    it('can sort category by `name`', function() {
        $this->seed([CategorySeeder::class]);

        $categories = Category::whereNull('parent_id')->get();

        Livewire::test(ListCategories::class)
            ->assertCanSeeTableRecords($categories)
            ->sortTable('name')
            ->assertCanSeeTableRecords($categories->sortBy('name'), inOrder:true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($categories->sortByDesc('name'), inOrder:true);
    });

    it('can search category by `name`', function() {
        $this->seed([CategorySeeder::class]);

        $categories = Category::whereNull('parent_id')->get();

        Livewire::test(ListCategories::class)
            ->assertCanSeeTableRecords($categories)
            ->searchTable($categories->first()->name)
            ->assertCanSeeTableRecords($categories->take(1))
            ->assertCanNotSeeTableRecords($categories->skip(1));
    });

    it('can filter category by `delete record` and `is_active`', function() {
        $this->seed([CategorySeeder::class]);

        $deletedCategory = Category::query()->first();
        $deletedCategory->delete();

        expect($deletedCategory->trashed())->toBeTrue();
        
        $activeCategories = Category::whereNull('parent_id')->get();
        $trashedCategories = Category::onlyTrashed()->get();

        Livewire::test(ListCategories::class)
            ->filterTable('trashed', '0')
            ->assertCanSeeTableRecords($trashedCategories)
            ->assertCanNotSeeTableRecords($activeCategories)
            ->resetTableFilters()
            ->filterTable('is_active', 1)
            ->assertCanSeeTableRecords($activeCategories->where('is_active', 1));



    });

    it('can restore category', function() {
        $this->seed([CategorySeeder::class]);

        $deletedCategory = Category::query()->first();
        $deletedCategory->delete();

        expect($deletedCategory->trashed())->toBeTrue();

        Livewire::test(ListCategories::class)
            ->filterTable('trashed', 'only')
            ->assertCanSeeTableRecords([$deletedCategory])
            ->assertTableActionVisible(RestoreAction::class, $deletedCategory)
            ->callTableAction(RestoreAction::class, $deletedCategory)
            ->assertNotified();

        expect($deletedCategory->refresh()->trashed())->toBeFalse();

    });
});

describe('create category', function() {
    it('can load page', function() {
        Livewire::test(CreateCategory::class)
            ->assertOk();
    });

    it('can create category', function() {
        $category = new Category([
            'name' => 'Testing'
        ]);

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => $category->name,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'slug' => 'testing',
            'image' => $category->image,
            'description' => $category->description,
            'is_active' => 1,
        ]);

        $category = new Category([
            'name' => 'Kebutuhan Hidup',
            'description' => 'Kategori kebutuhan hidup',
            'is_active' => 0,
        ]);

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => $category->name,
                'description' => $category->description,
                'is_active' => $category->is_active,
            ])
            ->call('create')
            ->assertHasNoFormErrors();
        
        assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'slug' => 'kebutuhan-hidup',
            'image' => $category->image,
            'description' => $category->description,
            'is_active' => 0,
        ]);
    });

    it('can create sub category', function() {
        $this->seed([CategorySeeder::class]);

        $category = Category::query()->first();
        $subCategory = new Category([
            'parent_id' => $category->id,
            'name' => 'Handphone dan Tablet'
        ]);

        Livewire::test(ViewCategory::class, [
            'record' => $category->id,
        ])
            ->assertSeeLivewire(ChildrenRelationManager::class);
        
        Livewire::test(ChildrenRelationManager::class, [
            'ownerRecord' => $category,
            'pageClass' => ViewCategory::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords($category->children)
            ->callTableAction('create', data: [
                'name' => $subCategory->name
            ]);

        assertDatabaseHas(Category::class, [
            'name' => $subCategory->name,
            'parent_id' => $category->id,
            'slug' => 'handphone-dan-tablet',
            'image' => $subCategory->image,
            'description' => $subCategory->description,
            'is_active' => 1,
        ]);
    });

    it('validate form data', function() {
        $this->seed([CategorySeeder::class]);

        $category = new Category([
            'name' => 'Elektronik'
        ]);

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name' => $category->name,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'slug' => 'unique'
            ])
            ->assertNotNotified();
    });
});

describe('edit category', function() {
    it('can load page', function() {
        $this->seed([CategorySeeder::class]);
    
        $category = Category::query()->first();
    
        Livewire::test(EditCategory::class, [
            'record' => $category->id,
        ])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Deskripsi dari kategori elektronik'
            ]);
    });

    it('can update category', function() {
        $this->seed([CategorySeeder::class]);
    
        $category = Category::query()->first();

        $category['name'] = 'Testing';
        $category['description'] = 'Deskripsi updated';
    
        Livewire::test(EditCategory::class, [
            'record' => $category->id,
        ])
            ->fillForm([
                'name' => $category->name,
                'description' => $category->description,
            ])
            ->call('save')
            ->assertNotified();
        
        assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'slug' => 'testing',
            'description' => $category->description,
        ]);
    });

    it('validate form data', function(array $data, array $errors) {
        $this->seed([CategorySeeder::class]);
    
        $category = Category::query()->first();

        $category['name'] = $data['name'];
        $category['description'] = $data['description'];
    
        Livewire::test(EditCategory::class, [
            'record' => $category->id,
        ])
            ->fillForm([
                'name' => $category->name,
                'description' => $category->description,
            ])
            ->call('save')
            ->assertHasFormErrors($errors)
            ->assertNotNotified();
    })->with([
        '`name` is required' => [
            [
                'name' => null,
                'description' => Str::random(256)
            ],
            ['name' => 'required']
        ],
        '`name` is max 255 character' => [
            [
                'name' => Str::random(256),
                'description' => Str::random(256)
            ],
            ['name' => 'max']
        ],
    ]);

    it('can delete category', function() {
        $this->seed([CategorySeeder::class]);

        $category = Category::query()->first();

        Livewire::test(EditCategory::class, [
            'record' => $category->id,
        ])
            ->callAction(DeleteAction::class)
            ->assertNotified()
            ->assertRedirect();
        
        assertSoftDeleted($category);
    });
});

describe('view category', function() {
    it('can load page', function() {
        $this->seed([CategorySeeder::class]);
    
        $category = Category::query()->first();
    
        Livewire::test(ViewCategory::class, [
            'record' => $category->id,
        ])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Deskripsi dari kategori elektronik'
            ]);
    });
});