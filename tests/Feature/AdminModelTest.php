<?php

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class);

test('Create admin test', function () {
    $this->seed([AdminSeeder::class]);

    $admins = Admin::all();

    expect($admins)->toBeCollection();
    assertDatabaseCount('admins', 2);

    $superadmin = $admins[0];
    $admin = $admins[1];

    expect($superadmin->name)->toBe('superadmin');
    expect($admin->name)->toBe('admin');
});
