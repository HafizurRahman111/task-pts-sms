<?php
namespace App\Interfaces;

interface SmsGatewayInterface
{
    /**
     * Send SMS to a list of phone numbers.
     *
     * @param array $phoneNumbers
     * @param string $message
     * @param array $userIds
     * @return array
     */
    public function sendSms(array $phoneNumbers, string $message, array $userIds): array;
}