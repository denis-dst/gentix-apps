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
        Role::updateOrCreate(['name' => 'superadmin']);
        Role::updateOrCreate(['name' => 'organizer']);
        Role::updateOrCreate(['name' => 'loket']);
        Role::updateOrCreate(['name' => 'gate']);
    }
}
