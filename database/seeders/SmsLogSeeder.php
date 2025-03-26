<?php

namespace Database\Seeders;

use App\Models\SmsLog;
use App\Models\Sms;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SmsLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmsLog::truncate();

        $smsRecords = Sms::all();

        if ($smsRecords->isEmpty()) {
            $this->command->warn('No SMS records found! Run SmsSeeder first.');
            return;
        }

        $faker = Faker::create();

        $allUserIds = collect($smsRecords->pluck('user_ids')->map(fn($ids) => json_decode($ids, true)))
            ->flatten()->unique()->toArray();

        $users = User::whereIn('id', $allUserIds)->get()->keyBy('id');

        $smsLogs = [];

        foreach ($smsRecords as $sms) {
            $userIds = json_decode($sms->user_ids, true);

            foreach ($userIds as $userId) {
                if (!isset($users[$userId])) {
                    continue;
                }

                $user = $users[$userId];

                $status = $faker->randomElement(['success', 'pending', 'failed']);
                $gatewayResponse = [
                    'phone_number' => $user->phone,
                    'status' => $status,
                    'sms_id' => $status === 'success' ? uniqid() : null,
                    'error' => $status === 'failed' ? 'Invalid number' : null,
                    'user_id' => $userId,
                ];

                $smsLogs[] = [
                    'sms_id' => $sms->id,
                    'user_id' => $userId,
                    'phone_number' => $user->phone,
                    'status' => $status,
                    'gateway_response' => json_encode($gatewayResponse),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($smsLogs)) {
            SmsLog::insert($smsLogs);
        }

        $this->command->info('SMS Logs table seeded successfully!');
    }
}
