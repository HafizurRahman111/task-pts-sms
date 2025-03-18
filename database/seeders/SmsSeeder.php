<?php

namespace Database\Seeders;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class SmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numberOfUsers = rand(1, 2);
        $userReferences = [];

        for ($i = 1; $i <= $numberOfUsers; $i++) {
            $userReferences[] = User::inRandomOrder()->first()->id;
        }

        // Create a Faker instance
        $faker = Faker::create();

        // Create 4 sms for the retrieved user
        for ($i = 0; $i < 3; $i++) {
            $gatewayResponse = "test";

            Sms::create([
                'purpose' => $faker->word,
                'student_ids' => $userReferences,
                'message' => $faker->sentence(3, true),
                'status' => $faker->randomElement(['success', 'failed']),
                'gateway_response' => $gatewayResponse,
                'created_at' => $faker->dateTimeBetween('now', '2025-12-31'),
            ]);
        }
    }
}
