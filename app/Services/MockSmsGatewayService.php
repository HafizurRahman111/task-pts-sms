<?php
namespace App\Services;

use App\Interfaces\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

class MockSmsGatewayService implements SmsGatewayInterface
{
    /**
     * Simulate sending SMS.
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

                // Simulate sending an SMS and returning a mock response
                $response = [
                    'phone_number' => $phoneNumber,
                    'status' => 'success',
                    'message_id' => 'mock_sms_id_' . $index,
                    'user_id' => $userId,
                ];

                // Log the success response for SMS
                Log::info("Mock SMS sent to $phoneNumber: $message");

                // Add the result to the results array
                $results[] = $response;
            } catch (\Exception $e) {
                // In case of failure, log the error and return the result
                $results[] = $this->formatErrorResult($phoneNumber, $e->getMessage(), $userId);

                // Log the error message for debugging purposes
                Log::error("Failed to send SMS to $phoneNumber: {$e->getMessage()}");
            }
        }

        // Determine the overall status of the SMS send operation
        $overallStatus = $this->determineOverallStatus($results);

        return [
            'overall_status' => $overallStatus,
            'results' => $results,
        ];
    }

    /**
     * Format an error result for failed SMS.
     *
     * @param string $phoneNumber
     * @param string $errorMessage
     * @param int|null $userId
     * @return array
     */
    protected function formatErrorResult(string $phoneNumber, string $errorMessage, ?int $userId): array
    {
        return [
            'phone_number' => $phoneNumber,
            'status' => 'failed',
            'error' => $errorMessage,
            'user_id' => $userId,
        ];
    }

    /**
     * Determine the overall status of the SMS sending process.
     *
     * @param array $results
     * @return string
     */
    protected function determineOverallStatus(array $results): string
    {
        // If any result is failed, we return partial failure, else success
        foreach ($results as $result) {
            if ($result['status'] === 'failed') {
                return 'partial_failure';
            }
        }

        return 'success';
    }
}