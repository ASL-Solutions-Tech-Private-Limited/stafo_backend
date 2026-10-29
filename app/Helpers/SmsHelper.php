<?php

namespace App\Helpers;

class SmsHelper
{
   
     
public static function sendOtp1($numbers, $otp)
{	
    $otp       = $otp;
	$sendermob = $numbers;	
	$sendermsg = "Dear User, Your Password : OR mpin is $otp. -From (OPENI)";
    return self::SendSMS($sendermob, $sendermsg, "1207166886120382392");

}



public static function SendSMS($DestinationAddress, $Message, $tempid)
{
    $apiToken = env('QUICKTEXT_API_TOKEN', 'YnIdxpw98OmCi6bcrjlVzFthAjnS3FV3');
    $encodedMsg = urlencode($Message);
    $encodedTo = urlencode(trim($DestinationAddress));
    $encodedTempId = urlencode(trim($tempid));
    $url = "https://quicktext.in/api/send-sms?apitoken={$apiToken}&to={$encodedTo}&sender=OPNMSG&message={$encodedMsg}&tempid={$encodedTempId}";
    
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
}



}