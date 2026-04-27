<?php

namespace App\Helpers;

class SmsHelper
{
    public static function sendOtp1($numbers, $otp)
    {
        $apiKey = env('FAST2SMS_API_KEY', 'STecaJIO6CoAkbljswDGmrzV5vqL7tn4H3hKPZi1d2YFUB80RWEcXRByI3lPO7JZzsnfGFrAkgbh2UeH');

        // Smartping API Base URL
        $baseUrl = 'https://api.smartping.ai/fe/api/v1/send';

        // Set required parameters
        $username = 'aslmtrpg.trans';  // Provided username
        $password = '8yi42';  // Provided password
        $unicode = 'false';  // Unicode set to false
        $from = 'ASLSTC';  // Sender ID
        $to = $numbers;  // Recipients phone number(s), passed as a parameter
        $dltPrincipalEntityId = '1701171888357691913';  // Provided DLT Principal Entity ID
        $dltContentId = '1707174083646521906';  // Provided DLT Content ID
        $text = 'To login to your STAFO account please use the OTP ' . $otp . ' - ASL SOLUTIONS TECH PRIVATE LIMITED';  // OTP message
        //$text = 'Your OTP is ' . $otp . ' - ASL SOLUTIONS TECH PRIVATE LIMITED';  // OTP message
        // Prepare the URL with the parameters
        $url = $baseUrl . '?' . http_build_query([
            'username' => $username,
            'password' => $password,
            'unicode' => $unicode,
            'from' => $from,
            'to' => $to,
            'dltPrincipalEntityId' => $dltPrincipalEntityId,
            'dltContentId' => $dltContentId,
            'text' => $text,
        ]);

        // Initialize cURL
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
            ],
        ]);

        // Execute cURL and get the response
        $response = curl_exec($curl);
        $err = curl_error($curl);

        // Close the cURL session
        curl_close($curl);

        // Handle errors if any
        if ($err) {
            return [
                'success' => false,
                'message' => 'cURL Error: ' . $err,
            ];
        }

        // Return the success response
        return [
            'success' => true,
            'message' => 'SMS sent successfully.',
            'response' => json_decode($response, true),
        ];
    }
}