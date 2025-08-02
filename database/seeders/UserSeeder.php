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
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Create regular users
        $users = [
            [
                'email' => 'john@example.com',
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'jane@example.com',
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'bob@example.com',
                'name' => 'Bob Johnson',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'alice@example.com',
                'name' => 'Alice Brown',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'charlie@example.com',
                'name' => 'Charlie Wilson',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $email = $userData['email'];
            unset($userData['email']);
            User::updateOrCreate(['email' => $email], $userData);
        }
    }
}
