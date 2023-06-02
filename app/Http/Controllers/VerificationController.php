<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PhoneNumberValidator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class VerificationController extends Controller
{
    protected $phoneNumberValidator;

    public function __construct(PhoneNumberValidator $phoneNumberValidator)
    {
        $this->phoneNumberValidator = $phoneNumberValidator;
    }

    public function verifyPhoneNumber(Request $request)
    {
        Log::info("Hit");
        $phoneNumber = $request->input('phone_number');
        Log::info($phoneNumber);
        $carrier = $this->phoneNumberValidator->validatePhoneNumber($phoneNumber);

        if ($carrier) {
            // Phone number is valid and associated with a known carrier in Kenya
            // You can proceed with sending an OTP or performing further verification steps

            return response()->json(['message' => 'Phone number is valid']);
        } else {
            // Phone number is invalid or associated with a special service
            // Handle the error accordingly

            return response()->json(['error' => 'Invalid phone number'], 400);
        }
    }

    public function verify(Request $request)
    {
     
        $client = new Client();
        $accessKey = env('NUMVERIFY_API_KEY');
        if (preg_match('/^(\+254|0)\d{9}$/', $request->phone_number)) {
            Log::info("True");
        }else {
            Log::info("False");
        }
        Log::info($request->phone_number);

        $response = $client->get("http://apilayer.net/api/validate", [
            'query' => [
                'access_key' => $accessKey,
                'number' => $request->phone_number,
                'country_code' => 'KE',
                'format' => 1
            ],
        ]);
        $responseData = json_decode($response->getBody(), true);

        return $responseData;
    }
}
