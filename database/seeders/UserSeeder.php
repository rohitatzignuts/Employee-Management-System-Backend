<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = bcrypt('password');
        $userEmail = 'admin@company.com';

        User::updateOrCreate([
            'first_name' => 'rohit',
            'last_name' => 'vispute',
            'role' => 'admin',
            'email' => $userEmail,
            'password' => $password,
            'company_id' => null,
            'emp_number' => null,
            'joining_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
