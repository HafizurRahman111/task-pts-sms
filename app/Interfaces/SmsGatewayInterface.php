<?php

namespace App\Interfaces;

interface SmsGatewayInterface
{
    /**
     * Send an SMS message to multiple phone numbers.
     *
     * @param array $phoneNumbers An array of phone numbers to send the SMS to.
     * @param string $message The message content.
     * @param string $purpose The purpose of the SMS.
     * @param array $studentIds An array of student IDs associated with the SMS.
     * @return array The response from the SMS gateway.
     */
    public function sendSms(array $phoneNumbers, string $message, string $purpose, array $studentIds): array;
}