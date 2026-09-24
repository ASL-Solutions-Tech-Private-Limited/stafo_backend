<?php

namespace App\Helpers;

class SmsHelper
{
    // public static function sendOtp1($numbers, $otp)
    // {
    //     $apiKey = env('FAST2SMS_API_KEY', 'STecaJIO6CoAkbljswDGmrzV5vqL7tn4H3hKPZi1d2YFUB80RWEcXRByI3lPO7JZzsnfGFrAkgbh2UeH');

    //     // Smartping API Base URL
    //     $baseUrl = 'https://api.smartping.ai/fe/api/v1/send';

    //     // Set required parameters
    //     $username = 'aslmtrpg.trans';  // Provided username
    //     $password = '8yi42';  // Provided password
    //     $unicode = 'false';  // Unicode set to false
    //     $from = 'ASLSTC';  // Sender ID
    //     $to = $numbers;  // Recipients phone number(s), passed as a parameter
    //     $dltPrincipalEntityId = '1701171888357691913';  // Provided DLT Principal Entity ID
    //     $dltContentId = '1707174083646521906';  // Provided DLT Content ID
    //     $text = 'To login to your STAFO account please use the OTP ' . $otp . ' - ASL SOLUTIONS TECH PRIVATE LIMITED';  // OTP message
    //     //$text = 'Your OTP is ' . $otp . ' - ASL SOLUTIONS TECH PRIVATE LIMITED';  // OTP message
    //     // Prepare the URL with the parameters
    //     $url = $baseUrl . '?' . http_build_query([
    //         'username' => $username,
    //         'password' => $password,
    //         'unicode' => $unicode,
    //         'from' => $from,
    //         'to' => $to,
    //         'dltPrincipalEntityId' => $dltPrincipalEntityId,
    //         'dltContentId' => $dltContentId,
    //         'text' => $text,
    //     ]);

    //     // Initialize cURL
    //     $curl = curl_init();

    //     // Set cURL options
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_SSL_VERIFYHOST => 0,
    //         CURLOPT_SSL_VERIFYPEER => 0,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "GET",
    //         CURLOPT_HTTPHEADER => [
    //             "cache-control: no-cache",
    //         ],
    //     ]);

    //     // Execute cURL and get the response
    //     $response = curl_exec($curl);
        
    //     //dd($response);
    //     $err = curl_error($curl);

    //     // Close the cURL session
    //     curl_close($curl);

    //     // Handle errors if any
    //     if ($err) {
    //         return [
    //             'success' => false,
    //             'message' => 'cURL Error: ' . $err,
    //         ];
    //     }

    //     // Return the success response
    //     return [
    //         'success' => true,
    //         'message' => 'SMS sent successfully.',
    //         'response' => json_decode($response, true),
    //     ];
    // }
    
    
public static function sendOtp1($numbers, $otp)
{	
    $otp       = $otp;
	$sendermob = $numbers;	
	$sendermsg = "Dear User, Your Password : OR mpin is $otp. -From (OPENI)";
// 	SendSMS($sendermob, $sendermsg, "1207166886120382392");
    return self::SendSMS($sendermob, $sendermsg, "1207166886120382392");

}



public static function SendSMS($DestinationAddress, $Message, $tempid)
{
    $Message = urlencode($Message);
    $url = "https://quicktext.in/api/send-sms?apitoken=YnIdxpw98OmCi6bcrjlVzFthAjnS3FV3&to=$DestinationAddress&sender=OPNMSG&message=$Message&tempid=$tempid";
    
    $fullurl =  str_replace(" ", "%20", $url);
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $fullurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    ));	
    $response = curl_exec($curl);
    curl_close($curl);
    
    
     return [
            'success' => true,
            'message' => 'SMS sent successfully.',
            'response' => json_decode($response, true),
        ];
    
    // $url = "https://whatsbot.tech/api/send_sms?api_token=717d31ac-9851-43dc-9cd3-54dda1365a65&mobile=91$DestinationAddress&message=$Message";
    // $fullurl =  str_replace(" ", "%20", $url);
    // $curl = curl_init();
    // curl_setopt_array($curl, array(
    // CURLOPT_URL => $fullurl,
    // CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_ENCODING => "",
    // CURLOPT_MAXREDIRS => 10,
    // CURLOPT_TIMEOUT => 0,
    // CURLOPT_FOLLOWLOCATION => true,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // CURLOPT_CUSTOMREQUEST => "GET",
    // ));	
    // $response = curl_exec($curl);
    // curl_close($curl);
    // return "";
}



}