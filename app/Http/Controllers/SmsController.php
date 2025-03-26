<?php
namespace App\Http\Controllers;

use App\Interfaces\SmsGatewayInterface;
use App\Jobs\SendSmsJob;
use App\Models\Sms;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    protected $smsGateway;

    public function __construct(SmsGatewayInterface $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    /**
     * Send SMS to users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSms(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:160',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the SMS record
        $sms = Sms::create([
            'subject' => $request->subject,
            'message' => $request->message,
            'user_ids' => json_encode($request->user_ids), // Store user_ids as JSON
        ]);

        // Fetch phone numbers for the given user IDs
        $phoneNumbers = $this->getPhoneNumbersFromUserIds($request->user_ids);

        if (empty($phoneNumbers)) {
            return response()->json(['error' => 'No valid phone numbers found for the provided user IDs.'], 404);
        }

        // Dispatch a job to send SMS asynchronously
        SendSmsJob::dispatch($sms, $phoneNumbers);

        return response()->json([
            'message' => 'SMS sending process has been queued.',
            'data' => $sms,
        ], 202);
    }

    /**
     * Fetch phone numbers for the given user IDs.
     *
     * @param array $userIds
     * @return array
     */
    protected function getPhoneNumbersFromUserIds(array $userIds): array
    {
        return User::whereIn('id', $userIds)
            ->pluck('phone')
            ->filter()
            ->toArray();
    }

    /**
     * Get SMS logs for a specific SMS.
     *
     * @param int $smsId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSmsLogs($smsId)
    {
        $logs = SmsLog::where('sms_id', $smsId)->get();

        return response()->json(['data' => $logs], 200);
    }

    /**
     * Display a listing of all SMS records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Eager load the associated SmsLog records for all SMS messages
            $messages = Sms::with('logs')->get();

            return response()->json(['data' => $messages], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching SMS records: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch SMS records.'], 500);
        }
    }

    /**
     * Display the specified SMS record.
     *
     * @param Sms $sms
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Sms $sms)
    {
        try {
            // Eager load the associated SmsLog records
            $sms->load('logs');

            return response()->json([
                'success' => true,
                'data' => [
                    'sms' => $sms,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching SMS record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch SMS record. Please try again later.'
            ], 500);
        }
    }

    /**
     * Delete the specified SMS record.
     *
     * @param Sms $sms
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Sms $sms)
    {
        try {
            $sms->delete();
            return response()->json([
                'success' => true,
                'message' => 'SMS record deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting SMS record: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete SMS record. Please try again later.'
            ], 500);
        }
    }


    public function showSmsForm(Request $request)
    {
        $users = User::select('id', 'name')->get();

        return view('sms.send_sms', compact('users'));
    }

}