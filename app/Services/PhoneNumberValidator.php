<?php

namespace App\Services;

use GuzzleHttp\Client;

class PhoneNumberValidator
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://apilayer.net',
        ]);
    }

    public function validatePhoneNumber($phoneNumber)
    {
        $accessKey = env('NUMVERIFY_API_KEY');
        $response = $this->client->get("/api/validate", [
            'query' => [
                'access_key' => $accessKey,
                'number' => $phoneNumber,
                'country_code' => 'KE',
                'format' => 1
            ],
        ]);
       

        $responseData = json_decode($response->getBody(), true);

        if ($responseData['valid'] && $responseData['line_type'] !== 'special_services') {
            return $responseData['carrier'];
        }

        return false;
    }
}
