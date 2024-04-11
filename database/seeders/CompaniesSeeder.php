<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

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
            'logo' => asset('storage/logos/logoone.png'),
            'location' => 'nowhere',
            'website' => 'https://www.company.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
