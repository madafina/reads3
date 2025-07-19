<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            StageSeeder::class,
            DivisionSeeder::class,
            TaskCategorySeeder::class,
            UserSeeder::class, // Butuh Role & Stage
            RequirementRuleSeeder::class, // Butuh Stage, Division, & TaskCategory
        ]);
    }
}