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
        Permission::firstOrCreate(['name' => 'manage-settings']); // stages, divisions, etc.
        Permission::firstOrCreate(['name' => 'verify-submissions']);
        Permission::firstOrCreate(['name' => 'view-all-submissions']);
        Permission::firstOrCreate(['name' => 'create-submission']);
        Permission::firstOrCreate(['name' => 'view-own-submissions']);
        
        // Buat Roles dan berikan permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        $dosenRole = Role::firstOrCreate(['name' => 'Dosen']);
        $dosenRole->givePermissionTo([
            'view-all-submissions',
            'verify-submissions', // Dosen bisa ikut memverifikasi
        ]);

        $residenRole = Role::firstOrCreate(['name' => 'Residen']);
        $residenRole->givePermissionTo([
            'create-submission',
            'view-own-submissions',
        ]);
    }
}