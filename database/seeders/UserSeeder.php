<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get warehouses for assignment
        $warehouses = Warehouse::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('No warehouses found. Users will not be assigned to warehouses.');
        }

        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'warehouse_id' => $warehouses->first()?->id,
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

            // Assign users to different warehouses
            if (!$warehouses->isEmpty()) {
                $userData['warehouse_id'] = $warehouses->random()->id;
            }

            User::updateOrCreate(['email' => $email], $userData);
        }
    }
}
