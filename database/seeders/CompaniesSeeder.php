<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::updateOrCreate([
            'name' => 'Temp Inc.',
            'cmp_email' => 'admin@companyFour.com',
            'logo' => null,
            'location' => 'nowhere',
            'website' => 'https://www.company.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate([
            'first_name' => 'test user',
            'last_name' => 'test user',
            'email' => 'test@company.com',
            'joining_date' => '2024-04-18',
            'role' => 'cmp_admin',
            'password' => bcrypt('password'),
            'company_id' => 1,
            'emp_number' => 'EMP00000',
        ]);
    }
}
