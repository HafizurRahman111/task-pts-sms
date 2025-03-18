<?php

namespace App\Services;

use App\Models\Sms; // Import the Sms model
use App\Interfaces\SmsGatewayInterface;

class MockSmsGatewayService implements SmsGatewayInterface
{
    public function sendSms(array $phoneNumbers, string $message, string $purpose, array $studentIds): array
    {
        $dummyResponse = [
            'sid' => 'SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'status' => 'success',
            'phone_numbers' => $phoneNumbers,
            'body' => $message,
            'date_sent' => now()->toISOString(),
            'student_ids' => json_encode($studentIds),
        ];

        Sms::create([
            'purpose' => $purpose,
            'student_ids' => json_encode($studentIds),
            'message' => $message,
            'status' => 'success',
            'gateway_response' => json_encode($dummyResponse),
        ]);

        return $dummyResponse;
    }
}