<?php


namespace App\Services;

use App\Interfaces\SmsGatewayInterface;
use App\Models\SmsLog;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;

class TwilioSmsGatewayService implements SmsGatewayInterface
{
    protected Client $twilio;
    protected string $fromNumber;

    public function __construct()
    {
        // Initialize Twilio client with credentials from config
        $this->twilio = new Client(
            config('services.twilio.sid'), // Twilio Account SID
            config('services.twilio.token') // Twilio Auth Token
        );

        // Set the Twilio phone number
        $this->fromNumber = config('services.twilio.from');
    }

    /**
     * Send SMS using Twilio.
     *
     * @param array $phoneNumbers
     * @param string $message
     * @param array $userIds
     * @return array
     */
    public function sendSms(array $phoneNumbers, string $message, array $userIds): array
    {
        $results = [];

        foreach ($phoneNumbers as $index => $phoneNumber) {
            $userId = $userIds[$index] ?? null;

            try {
                if (!$userId) {
                    throw new \Exception("User ID is missing for phone number: $phoneNumber");
                }

                // Send SMS using Twilio
                $twilioMessage = $this->twilio->messages->create(
                    $phoneNumber, // To
                    [
                        'from' => $this->fromNumber, // Twilio phone number
                        'body' => $message, // SMS message
                    ]
                );

                // Check the Twilio response for success
                if ($twilioMessage->sid) {
                    $results[] = $this->formatSuccessResult($phoneNumber, $twilioMessage->sid, $userId);
                } else {
                    throw new TwilioException('Twilio response SID is empty.');
                }
            } catch (TwilioException $e) {
                // Log failure
                $results[] = $this->formatErrorResult($phoneNumber, $e->getMessage(), $userId);
            } catch (\Exception $e) {
                // Handle any other exceptions that may arise
                $results[] = $this->formatErrorResult($phoneNumber, $e->getMessage(), $userId);
            }
        }

        // Determine overall status
        $overallStatus = $this->determineOverallStatus($results);

        return [
            'overall_status' => $overallStatus,
            'results' => $results,
        ];
    }

    // Format methods remain the same
    protected function formatSuccessResult(string $phoneNumber, string $messageId, ?int $userId): array
    {
        return [
            'phone_number' => $phoneNumber,
            'status' => 'success',
            'message_id' => $messageId,
            'user_id' => $userId,
        ];
    }

    protected function formatErrorResult(string $phoneNumber, string $errorMessage, ?int $userId): array
    {
        return [
            'phone_number' => $phoneNumber,
            'status' => 'failed',
            'error' => $errorMessage,
            'user_id' => $userId,
        ];
    }

    protected function determineOverallStatus(array $results): string
    {
        foreach ($results as $result) {
            if ($result['status'] === 'failed') {
                return 'partial_failure';
            }
        }

        return 'success';
    }
}
