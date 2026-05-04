<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleOptionsSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate first to prevent duplicates
        DB::table('role_options')->truncate();

        $roles = ['admin', 'employee'];

        foreach ($roles as $i => $role) {
            DB::table('role_options')->insert([
                'label'      => $role,
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}