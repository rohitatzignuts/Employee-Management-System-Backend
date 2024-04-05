<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = bcrypt('password');
        $userEmail = 'admin@company.com';

        DB::table('users')->insert([
            'first_name' => 'rohit',
            'last_name' => 'vispute',
            'role' => 'admin',
            'email' => $userEmail,
            'password' => $password,
            'company_id' => 1,
            'emp_number' => null,
            'joining_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
