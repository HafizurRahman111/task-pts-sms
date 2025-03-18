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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '123456789',
                'password' => Hash::make('pass1234'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regular User-1',
                'email' => 'user@example.com',
                'phone' => '127456789',
                'password' => Hash::make('pass1234'),
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Regular User-2',
                'email' => 'user2@example.com',
                'phone' => '127458789',
                'password' => Hash::make('pass1234'),
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->call([
            SmsSeeder::class,
        ]);

    }
}
