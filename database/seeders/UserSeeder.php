<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['username' => 'alam'],
            [
                'name' => 'Alam',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'falah'],
            [
                'name' => 'Falah',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );
    }
}
