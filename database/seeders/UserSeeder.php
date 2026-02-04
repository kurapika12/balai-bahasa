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
                'name' => 'Muhammad Aslam Hidayat',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'falah'],
            [
                'name' => 'Falah Zikri',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'zahra'],
            [
                'name' => 'Wa Ode Zahra Ramadani',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'pingki'],
            [
                'name' => 'Wa Ode Fitri Nur Ramadhani',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'adhan'],
            [
                'name' => 'La Ode Muhammad Rahmad Adhan Halu',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        User::firstOrCreate(
            ['username' => 'izhar'],
            [
                'name' => 'Muhammad Izzharudin',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

                User::firstOrCreate(
            ['username' => 'izhar'],
            [
                'name' => 'Izhar',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );
    }
}
