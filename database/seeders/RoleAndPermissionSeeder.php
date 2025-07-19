<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Permissions
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'manage-settings']); 
        Permission::firstOrCreate(['name' => 'verify-submissions']);
        Permission::firstOrCreate(['name' => 'view-all-submissions']);
        Permission::firstOrCreate(['name' => 'create-submission']);
        Permission::firstOrCreate(['name' => 'view-own-submissions']);
        Permission::firstOrCreate(['name' => 'view-students']);
        
        // Buat Roles dan berikan permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'manage-users',
            'manage-settings',
            'view-all-submissions',
            'verify-submissions', 
        ]);

        $dosenRole = Role::firstOrCreate(['name' => 'Dosen']);
        $dosenRole->givePermissionTo([
            'view-all-submissions',
            'view-students'
        ]);

        $residenRole = Role::firstOrCreate(['name' => 'Residen']);
        $residenRole->givePermissionTo([
            'create-submission',
            'view-own-submissions',
        ]);
    }
}