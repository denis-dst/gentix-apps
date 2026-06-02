<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::updateOrCreate(['name' => 'Superadmin']);
        Role::updateOrCreate(['name' => 'Penyedia Event']);
        Role::updateOrCreate(['name' => 'Petugas Loket']);
        Role::updateOrCreate(['name' => 'Petugas Gate']);
    }
}
