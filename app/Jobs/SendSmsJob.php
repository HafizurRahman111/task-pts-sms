<?php

namespace App\Jobs;

use App\Models\Sms;
use App\Services\MockSmsGatewayService;
use App\Services\TwilioSmsGatewayService;
use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Sms $sms, private array $phoneNumbers)
    {
    }

    public function handle(MockSmsGatewayService $smsService): void
    {
        $message = $this->sms->message;
        $userIds = json_decode($this->sms->user_ids, true);

        // Send SMS using the SMS gateway service
        $responses = $smsService->sendSms($this->phoneNumbers, $message, $userIds);

        // Update the SmsLog entries with the sms_id
        foreach ($responses['results'] as $response) {
            try {
                SmsLog::create([
                    'sms_id' => $this->sms->id,
                    'user_id' => $response['user_id'],
                    'phone_number' => $response['phone_number'],
                    'status' => $response['status'],
                    'gateway_response' => json_encode($response), // Ensure the response is stored as JSON
                ]);

                Log::info("SmsLog created for phone number: {$response['phone_number']}");
            } catch (\Exception $e) {
                Log::error("Failed to create SmsLog for phone number: {$response['phone_number']}. Error: {$e->getMessage()}");
            }
        }

        // Update the SMS status based on the overall response
        try {
            $this->sms->update(['status' => $responses['overall_status']]);
            Log::info("Sms status updated for SMS ID: {$this->sms->id} to {$responses['overall_status']}");
        } catch (\Exception $e) {
            Log::error("Failed to update SMS status for SMS ID: {$this->sms->id}. Error: {$e->getMessage()}");
        }
    }
}
