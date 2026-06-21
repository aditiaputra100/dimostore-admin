<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = new Admin();
        $superadmin['name'] = 'superadmin';
        $superadmin['email'] = 'superadmin@email.com';
        $superadmin['password'] = 'superadmin';
        $superadmin['role'] = 'super_admin';

        $superadmin->save();

        $admin = new Admin();
        $admin['name'] = 'admin';
        $admin['email'] = 'admin@email.com';
        $admin['password'] = 'admin';
        $admin['role'] = 'admin';

        $admin->save();
    }
}
