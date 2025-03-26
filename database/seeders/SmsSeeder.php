<?php

namespace Database\Seeders;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sms::truncate();

        // Fetch all student IDs
        $studentIds = User::where('role', 'student')->pluck('id')->toArray();

        // Check if there are any students
        if (empty($studentIds)) {
            $this->command->warn('No students found in the users table!');
            return;
        }

        // Shuffle the student IDs randomly
        shuffle($studentIds);

        // Get 5 random student IDs, or fewer if there are not enough students
        $randomStudentIds = array_slice($studentIds, 0, 5);

        // Initialize Faker
        $faker = Faker::create();

        // SMS records to be created
        $smsRecords = [
            [
                'subject' => 'Reminder',
                'message' => 'Assignment due tomorrow.',
                'user_ids' => $randomStudentIds,
            ],
            [
                'subject' => 'Notification',
                'message' => 'Class canceled due to weather.',
                'user_ids' => $randomStudentIds,
            ],
            [
                'subject' => 'Alert',
                'message' => 'Library books due soon.',
                'user_ids' => $randomStudentIds,
            ],
            [
                'subject' => 'Promotion',
                'message' => 'New course available! Enroll now.',
                'user_ids' => $randomStudentIds,
            ]
        ];

        foreach ($smsRecords as $smsRecord) {
            Sms::create([
                'subject' => $smsRecord['subject'],
                'message' => $smsRecord['message'],
                'user_ids' => json_encode($smsRecord['user_ids']),
            ]);
        }

        $this->command->info('SMS table seeded successfully!');
    }
}