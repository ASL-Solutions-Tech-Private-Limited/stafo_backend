<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Google\Client as GoogleClient;

class Helper
{
    public static function convert($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',
            10000000 => 'crore'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . self::convert(abs($number));
        }

        $string = $fraction = null;

        if (strpos((string)$number, '.') !== false) {
            [$number, $fraction] = explode('.', (string)$number);
        }

        $number = (int) $number;

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int)($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert($remainder);
                }
                break;
            default:
                if ($number >= 10000000) {
                    $baseUnit = 10000000;
                } elseif ($number >= 100000) {
                    $baseUnit = 100000;
                } elseif ($number >= 1000) {
                    $baseUnit = 1000;
                }

                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;

                $string = self::convert($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert($remainder);
                }
                break;
        }

        // Handle decimal (paise, etc.)
        if ($fraction !== null && (int)$fraction > 0) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string)$fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function sendPushNotification($fcm,$notification)
    {
        
        // $user = CompanyDetail::find($company_id);
        // $fcm = $user->fcm_token;

        if ($fcm) {            

            $title = 'Stafo'; //$request->title;
            $description = $notification;
            $projectId = 'stafo-38601';//config('services.fcm.project_id'); # INSERT COPIED PROJECT ID

            $credentialsFilePath = Storage::path('json/fcm.json');
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();

            $access_token = $token['access_token'];
            //dd($access_token);
            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];
            
            $data = [
                "message" => [
                    "token" => $fcm,
                    "notification" => [
                        "title" => $title,
                        "body" => $description,
                    ],
                ]
            ];
            $payload = json_encode($data);
            //dd($payload);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            //$response = json_decode($response);
            // dd($response);
            // if (isset($response->error)) {
            //     return response()->json([
            //         'message' => 'Unable to send notification',
            //         'response' => $response->error->message
            //     ]);
               
            // } else {
            //     return response()->json([
            //         'message' => 'Notification has been sent',
            //         'response' => json_decode($response, true)
            //     ]);
                
            // }
        }

    }
}