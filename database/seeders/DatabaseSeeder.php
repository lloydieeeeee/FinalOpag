<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Position;
use App\Models\Employee;
use App\Models\UserCredential;
use App\Models\UserAccess;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $posAdmin = Position::create(['position_name' => 'Planning Officer IV']);
        $posEmp   = Position::create(['position_name' => 'Agricultural Technologist']);

        // Admin
        $admin = Employee::create([
            'last_name' => 'Belardo', 'first_name' => 'Raphy B.',
            'department_id' => 1, 'position_id' => $posAdmin->position_id,
            'hire_date' => '2020-01-01', 'salary' => 50000,
        ]);
        UserCredential::create([
            'employee_id' => $admin->employee_id,
            'password_hash' => Hash::make('admin123'),
        ]);
        UserAccess::create(['employee_id' => $admin->employee_id, 'user_access' => 'admin']);

        // Employee
        $emp = Employee::create([
            'last_name' => 'Doe', 'first_name' => 'Jane', 'middle_name' => 'A.',
            'department_id' => 1, 'position_id' => $posEmp->position_id,
            'hire_date' => '2022-06-15', 'salary' => 30000,
        ]);
        UserCredential::create([
            'employee_id' => $emp->employee_id,
            'password_hash' => Hash::make('employee123'),
        ]);
        UserAccess::create(['employee_id' => $emp->employee_id, 'user_access' => 'employee']);
    }
}