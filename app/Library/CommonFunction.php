<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class CommonFunction
{
    private static $instance = null;

    // Private constructor to prevent multiple instances
    private function __construct() {}

    // Static method to get the single instance
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new CommonFunction();
        }
        return self::$instance;
    }

    //*********** Encryption Function *********************
    public static function encrypt($plainText, $key)
    {
        $secretKey = self::hextobin(md5($key));  // Use self to call static method
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        return bin2hex($encryptedText);
    }

    //*********** Decryption Function *********************
    public static function decrypt($encryptedText, $key)
    {
        $key = self::hextobin(md5($key));  // Use self to call static method
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = self::hextobin($encryptedText);  // Use self to call static method
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    //********** Hexadecimal to Binary function for php 4.0 version ********
    // public static function hextobin($hexString)
    // {
    //     // dd($hexString);
    //     $length = strlen($hexString);
    //     $binString = "";
    //     $count = 0;
    //     while ($count < $length) {
    //         $subString = substr($hexString, $count, 2);
    //         $packedString = pack("H*", $subString);
    //         // dd($packedString);
    //         $binString .= $packedString;
    //         $count += 2;
    //     }
    //     return $binString;
    // }

    public static function hextobin($hexString)
    {
        // Remove any non-hexadecimal characters
        $hexString = preg_replace('/[^0-9A-Fa-f]/', '', $hexString);

        // Check if the length is even (Hexadecimal should have even length)
        if (strlen($hexString) % 2 != 0) {
            $hexString = '0' . $hexString;  // Pad with leading zero if odd length
        }

        $length = strlen($hexString);
        $binString = "";
        $count = 0;

        while ($count < $length) {
            // Extract 2 characters (1 byte)
            $subString = substr($hexString, $count, 2);

            // Ensure the substring is a valid hex digit before packing
            if (ctype_xdigit($subString)) {
                $packedString = pack("H*", $subString);
                $binString .= $packedString;
            } else {
                throw new Exception("Invalid hexadecimal string: $subString");
            }

            $count += 2;
        }
        return $binString;
    }


    //********** To generate random String ********
    public static function generateRandomString($length = 35)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public static function mdmRequestNew($blno, $requestId)
    {

        $plainText = '<?xml version="1.0" encoding="UTF-8"?><billerInfoRequest>
    <billerId>' . $blno . '</billerId>
</billerInfoRequest>';

$key = env('BILLENCRYPTION_KEY', '2A2C80485B5E3B1BE5D99D67E2DF8309');
$encrypt_xml_data = self::encrypt($plainText, $key);

$data = [
'accessCode' => env('ACCESS_CODE', 'AVQQ81XX06RT98DLVC'),
'requestId' => $requestId,
'ver' => "1.0",
'instituteId' => env('INSTITUTE_ID', 'AC62'),
];

$parameters = http_build_query($data);
$xml = null;

if (app()->environment() !== 'local') {
$url = "https://api.billavenue.com/billpay/extMdmCntrl/mdmRequestNew/xml?" . $parameters;

$response = Http::withHeaders(['Content-Type' => 'text/xml'])
->timeout(30)
->post($url, $encrypt_xml_data);

$response = self::decrypt($response->body(), $key);
} else {
$response = '
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<billerInfoResponse>
    <responseCode>000</responseCode>
    <biller>
        <billerId>OTME00005XXZ43</billerId>
        <billerName>OTME</billerName>
        <billerCategory>Mobile Postpaid</billerCategory>
        <billerAdhoc>true</billerAdhoc>
        <billerCoverage>IND</billerCoverage>
        <billerFetchRequiremet>MANDATORY</billerFetchRequiremet>
        <billerPaymentExactness>EXACT</billerPaymentExactness>
        <billerSupportBillValidation>NOT_SUPPORTED</billerSupportBillValidation>
        <supportPendingStatus>No</supportPendingStatus>
        <supportDeemed>No</supportDeemed>
        <billerTimeout></billerTimeout>
        <billerInputParams>
            <paramInfo>
                <paramName>a</paramName>
                <dataType>NUMERIC</dataType>
                <isOptional>false</isOptional>
                <minLength>1</minLength>
                <maxLength>3</maxLength>
            </paramInfo>
            <paramInfo>
                <paramName>a b</paramName>
                <dataType>NUMERIC</dataType>
                <isOptional>false</isOptional>
                <minLength>1</minLength>
                <maxLength>3</maxLength>
            </paramInfo>
            <paramInfo>
                <paramName>a b c</paramName>
                <dataType>NUMERIC</dataType>
                <isOptional>false</isOptional>
                <minLength>1</minLength>
                <maxLength>3</maxLength>
            </paramInfo>
            <paramInfo>
                <paramName>a b c d</paramName>
                <dataType>NUMERIC</dataType>
                <isOptional>false</isOptional>
                <minLength>1</minLength>
                <maxLength>3</maxLength>
            </paramInfo>
            <paramInfo>
                <paramName>a b c d e</paramName>
                <dataType>NUMERIC</dataType>
                <isOptional>false</isOptional>
                <minLength>1</minLength>
                <maxLength>3</maxLength>
            </paramInfo>
        </billerInputParams>
        <billerAmountOptions>BASE_BILL_AMOUNT,Fixed Charges,,|Additional Charges,BASE_BILL_AMOUNT,,|Late Payment
            Fee,BASE_BILL_AMOUNT,Fixed Charges,|Additional Charges,Late Payment Fee,BASE_BILL_AMOUNT,|Additional
            Charges,Late Payment Fee,BASE_BILL_AMOUNT,Fixed Charges|BASE_BILL_AMOUNT,,,|Late Payment Fee,,,|Fixed
            Charges,,,|Additional Charges,,,|Late Payment Fee,BASE_BILL_AMOUNT,,</billerAmountOptions>
        <billerPaymentModes>NEFT, CASH, INTERNET BANKING, DEBIT CARD, UPI, CREDIT CARD, WALLET, IMPS, PREPAID CARD
        </billerPaymentModes>
        <billerDescription></billerDescription>
        <rechargeAmountInValidationRequest></rechargeAmountInValidationRequest>
        <billerPaymentChannels>
            <paymentChannelInfo>
                <paymentChannelName>MOBB</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>AGT</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>INTB</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>ATM</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>BSC</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>MPOS</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>INT</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>MOB</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>KIOSK</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>POS</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
            <paymentChannelInfo>
                <paymentChannelName>BNKBRNCH</paymentChannelName>
                <minAmount>0</minAmount>
                <maxAmount>20000000</maxAmount>
            </paymentChannelInfo>
        </billerPaymentChannels>
    </biller>
</billerInfoResponse>';
}

$response = trim($response);
$response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
$response = ltrim($response);
libxml_use_internal_errors(true);

$xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);

if ($xml === false) {
$errors = libxml_get_errors();
return json_encode([
'success' => false,
'message' => 'Invalid XML returned.',
'error' => array_map(fn($e) => $e->message, $errors),
'raw_response' => $response
]);
}

// dd($xml);
return json_encode($xml);
}


// Bill Fetch Request
public function billFetchRequest($xml, $requestId)
{
$key = env('BILLENCRYPTION_KEY', 'default-key');
$encrypt_xml_data = self::encrypt($xml, $key);

$data = [
'accessCode' => env('ACCESS_CODE', 'AVQQ81XX06RT98DLVC'),
'requestId' => $requestId,
'encRequest' => $encrypt_xml_data,
'ver' => "1.0",
'instituteId' => env('INSTITUTE_ID', 'AC62'),
];

$parameters = http_build_query($data);

$url = "https://api.billavenue.com/billpay/extBillCntrl/billFetchRequest/xml";
$response = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
->timeout(30)
->post($url, $parameters);

$response = self::decrypt($response->body(), $key);
return $response;
}

// Bill Pay Request
public function billPayRequest($xml, $requestId)
{
$key = env('BILLENCRYPTION_KEY', 'default-key');
$encrypt_xml_data = self::encrypt($xml, $key);

$data = [
'accessCode' => env('ACCESS_CODE', 'AVQQ81XX06RT98DLVC'),
'requestId' => $requestId,
'encRequest' => $encrypt_xml_data,
'ver' => "1.0",
'instituteId' => env('INSTITUTE_ID', 'AC62'),
];

$parameters = http_build_query($data);

$url = "https://api.billavenue.com/billpay/extBillPayCntrl/billPayRequest/xml";
$response = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
->timeout(30)
->post($url, $parameters);

$response = self::decrypt($response->body(), $key);
return $response;
}

// Status check for CC
public function statusCheckCC($TxnId, $requestId)
{
$xml = '
<?xml version="1.0" encoding="UTF-8"?>
<transactionStatusReq>
    <trackType>REQUEST_ID</trackType>
    <trackValue>' . $TxnId . '</trackValue>
</transactionStatusReq>';
$key = env('BILLENCRYPTION_KEY', 'default-key');
$encrypt_xml_data = self::encrypt($xml, $key);

$data = [
'accessCode' => env('ACCESS_CODE', 'AVQQ81XX06RT98DLVC'),
'requestId' => $requestId,
'encRequest' => $encrypt_xml_data,
'ver' => "1.0",
'instituteId' => env('INSTITUTE_ID', 'AC62'),
];

$parameters = http_build_query($data);

$url = "https://api.billavenue.com/billpay/transactionStatus/fetchInfo/xml";
$response = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
->timeout(30)
->post($url, $parameters);

$response = self::decrypt($response->body(), $key);
return $response;
}

// Function for handling complaints
public function extComplaints($xml, $requestId)
{
$key = env('BILLENCRYPTION_KEY', 'default-key');
$encrypt_xml_data = self::encrypt($xml, $key);

$data = [
'accessCode' => env('ACCESS_CODE', 'AVQQ81XX06RT98DLVC'),
'requestId' => $requestId,
'encRequest' => $encrypt_xml_data,
'ver' => "1.0",
'instituteId' => env('INSTITUTE_ID', 'AC62'),
];

$parameters = http_build_query($data);

$url = "https://api.billavenue.com/billpay/extComplaints/register/xml";
$response = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
->timeout(30)
->post($url, $parameters);

$response = self::decrypt($response->body(), $key);
return $response;
}

// Parse the XML response from bill request
public static function parseBillRequest($response)
{
try {
return json_encode(simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA));
} catch (Exception $e) {
return 'Error: ' . $e->getMessage();
}
}
}