<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
     public function run(): void
    {
        $company = Company::factory()->create([
            'name'  => 'Dan Technologies',
            'email' => 'admin@dan.com',
        ]);

        $admin = User::factory()->admin()->create([
            'company_id' => $company->id,
            'name'       => 'Admin User',
            'username'   => 'admin_dan',
            'email'      => 'admin@dan.com',
            'password'   => bcrypt('password'),
        ]);

        $manager = User::factory()->manager()->create([
            'company_id' => $company->id,
            'name'       => 'Manager User',
            'username'   => 'manager_dan',
            'email'      => 'manager@dan.com',
        ]);

        $employee = User::factory()->create([
            'company_id' => $company->id,
            'name'       => 'Employee User',
            'username'   => 'employee_dan',
            'email'      => 'employee@dan.com',
        ]);

        Expense::factory(20)->create([
            'company_id' => $company->id,
            'user_id'    => $employee->id,
        ]);

        Expense::factory(10)->create([
            'company_id' => $company->id,
            'user_id'    => $manager->id,
        ]);

        $this->command->info('Seeded: admin@dan.com / password');
    }
}
