<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Angga Risky Setiawan',
                'email' => 'angga@buildwithangga.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'phone' => '081234567890',
            ],
            [
                'name' => 'Masayoshi Tanaka',
                'email' => 'masayoshi@hakone.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'phone' => '081298765432',
            ],
            [
                'name' => 'Shayna Putri',
                'email' => 'shayna@hakone.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_RENTER,
                'phone' => '081311223344',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@hakone.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_RENTER,
                'phone' => '081355667788',
            ],
        ];

        foreach ($users as $user) {
            if (! User::where('email', $user['email'])->exists()) {
                User::create($user);
            }
        }
    }
}
