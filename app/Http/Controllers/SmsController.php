<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Services\MockSmsGatewayService;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(MockSmsGatewayService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'purpose' => 'required|string|max:255',
                'student_ids' => 'required|array',
            ]);

            $phoneNumbers = $this->getPhoneNumbersFromStudentIds($validatedData['student_ids']);

            if (empty($phoneNumbers)) {
                return response()->json(['error' => 'No phone numbers found for the provided student IDs.'], 404);
            }

            $gatewayResponse = $this->smsService->sendSms(
                $phoneNumbers,
                $validatedData['message'],
                $validatedData['purpose'],
                $validatedData['student_ids'],
            );

            return response()->json(['data' => $gatewayResponse, 'message' => 'SMS sent successfully!'], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error sending SMS: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send SMS'], 500);
        }
    }

    protected function getPhoneNumbersFromStudentIds(array $studentIds): array
    {
        return User::whereIn('id', $studentIds)->pluck('phone')->toArray();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $messages = Sms::all();
            return response()->json(['data' => $messages], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch messages'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Sms $sms)
    {
        try {
            if (!$sms) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sms not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $sms
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching sms: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch sms. Please try again later.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sms $sms)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sms $sms)
    {


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sms $sms)
    {

    }
}
