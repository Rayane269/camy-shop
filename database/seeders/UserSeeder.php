<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Caissiers
        User::create([
            'name' => 'Caissier 1',
            'email' => 'caissier@camy.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Caissier 2',
            'email' => 'caissier2@camy.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Rahmadani',
            'email' => 'hote@raymultitech.com',
            'password' => Hash::make('password'),
            'role' => 'caissier',
            'email_verified_at' => now(),
        ]);
    }
}
