<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin Aplikasi',
            'email' => 'admin@contoh.com',
            'password' => Hash::make('password'), // Password di-hash menjadi 'password'
            'role' => 'admin',
        ]);
    }
}