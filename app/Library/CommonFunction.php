<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class CommonFunction {
		private static $instance = null;

		// Private constructor to prevent multiple instances
         private function __construct() {
        
         }

	    // Static method to get the single instance
	    public static function getInstance() {
	        if (self::$instance === null) {
	            self::$instance = new CommonFunction();
	        }
	        return self::$instance;
	    }

	 //*********** Encryption Function *********************
    public function encryptt($plainText, $key) {
       
        $secretKey = $this->hextobin(md5($key));  
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }
    
    //*********** Decryption Function *********************
    public function decryptt($encryptedText, $key) {
        
        $key =$this->hextobin(md5($key));  
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        //$encryptedText = hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }
    
    //********** Hexadecimal to Binary function for php 4.0 version ********
    public function hextobin($hexString) {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }
    
            $count += 2;
        }
        return $binString;
    }
    
    //********** To generate ramdom String ********
    public static function generateRandomString($length = 35) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function mdmRequestNew($blno,$requestId){
        /*
            Agent Institution ID 	AS55
            Agent Institution Name 	ASL Solutions Private Limited
            Access Code 	AVTT22LO03UF72FGDB
            Working Key 	9476883E8E42D993FC49728E3BED16E1
            stgapi.billavenue.com
            
            Institute ID:	AC62
            Access Code:	AVQQ81XX06RT98DLVC
            Working Key:	2A2C80485B5E3B1BE5D99D67E2DF8309
            api.billavenue.com
        */
        $plainText = '<?xml version="1.0" encoding="UTF-8"?><billerInfoRequest><billerId>'.$blno.'</billerId></billerInfoRequest>';
       
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data= $this->encryptt($plainText, $key);
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        
        $parameters = http_build_query($data);
        if(!isLocal()){
        
        $url = "https://api.billavenue.com/billpay/extMdmCntrl/mdmRequestNew/xml?" . $parameters;
        
        //return $url.' - '.$encrypt_xml_data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encrypt_xml_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $response = $this->decryptt($result, $key);
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
    	}else{
    	 $response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
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
        <billerAmountOptions>BASE_BILL_AMOUNT,Fixed Charges,,|Additional Charges,BASE_BILL_AMOUNT,,|Late Payment Fee,BASE_BILL_AMOUNT,Fixed Charges,|Additional Charges,Late Payment Fee,BASE_BILL_AMOUNT,|Additional Charges,Late Payment Fee,BASE_BILL_AMOUNT,Fixed Charges|BASE_BILL_AMOUNT,,,|Late Payment Fee,,,|Fixed Charges,,,|Additional Charges,,,|Late Payment Fee,BASE_BILL_AMOUNT,,</billerAmountOptions>
        <billerPaymentModes>NEFT, CASH, INTERNET BANKING, DEBIT CARD, UPI, CREDIT CARD, WALLET, IMPS, PREPAID CARD</billerPaymentModes>
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
       
        
        return json_encode($xml);
    }
    
    /* CC bill Fetch Request*/
    
    // function billFetchRequest($xml,$requestId){
    //     $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
    //     $encrypt_xml_data = $this->encryptt($xml, $key);
    //     //echo "<pre>".htmlentities(decrypt($encrypt_xml_data, $key)).'<br><br>';
        
    //     $data['accessCode'] = "AVQQ81XX06RT98DLVC";
    //     $data['requestId'] = $requestId;
    //     $data['encRequest'] = $encrypt_xml_data;
    //     $data['ver'] = "1.0";
    //     $data['instituteId'] = "AC62";
    //     $parameters = http_build_query($data);
    //     //echo$parameters.'<br>';
    //     $url = "https://api.billavenue.com/billpay/extBillCntrl/billFetchRequest/xml";
    //     //echo '<br>'.$encrypt_xml_data;
        
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //     $result = curl_exec($ch);
    //     //echo '<br><br>Encrypted Response: ' . $result . "<br><br>";
    //     $response =  $this->decryptt($result, $key);
    //     $xmlrs=htmlentities($response);
    //     //echo "<pre>".$xmlrs;
    //     return $response;
    // }



        function billFetchRequest($xml,$requestId){
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data = $this->encryptt($xml, $key);
      
        
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        $parameters = http_build_query($data);
        $url = "https://api.billavenue.com/billpay/extBillCntrl/billFetchRequest/xml";
        
        $postArray = [
           "url" => $url,
           "parameters" => $parameters
        ];
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payzones.net/api/providerCall/bbps/fetchBill.php",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postArray),

        ));
        $result = curl_exec($curl);
        curl_close($curl);
        
        //echo '<br><br>Encrypted Response: ' . $result . "<br><br>";
        $response =  $this->decryptt($result, $key);
        $xmlrs=htmlentities($response);
        //echo "<pre>".$xmlrs;
        return $response;
    }


    function billPayRequest($xml,$requestId){
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data = $this->encryptt($xml, $key);
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        $parameters = http_build_query($data);
        //echo$parameters.'<br>';
        $url = "https://api.billavenue.com/billpay/extBillPayCntrl/billPayRequest/xml";
        //return '<br>'.$encrypt_xml_data;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        //echo '<br><br>Encrypted Response: ' . $result . "<br><br>";
        $response =  $this->decryptt($result, $key);
        $xmlrs=htmlentities($response);
        //echo "<pre>".$xmlrs;
        return $response;
    }
    
    function status_checkcc($TxnId,$requestId)
    {
        $xml='<?xml version="1.0" encoding="UTF-8"?><transactionStatusReq><trackType>REQUEST_ID</trackType><trackValue>'.$TxnId.'</trackValue></transactionStatusReq>';
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data = $this->encryptt($xml, $key);
        
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        //echo $data['requestId'].'<br>';
        $parameters = http_build_query($data);
        
        $url = "https://api.billavenue.com/billpay/transactionStatus/fetchInfo/xml";
        //echo '<br>'.$encrypt_xml_data;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $response = $this->decryptt($result, $key);
        return $response;
    }
    
    function extComplaints($xml,$requestId){
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data =  $this->encryptt($xml, $key);
        //echo "<pre>".htmlentities(decryptt($encrypt_xml_data, $key)).'<br><br>';
        
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        //echo $data['requestId'].'<br>';
        $parameters = http_build_query($data);
        
        $url = "https://api.billavenue.com/billpay/extComplaints/register/xml";
        //echo '<br>'.$encrypt_xml_data;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $response =  $this->decryptt($result, $key);
        return $response;
    }
    function extComplaintstrack($xml,$requestId){
        $key = "2A2C80485B5E3B1BE5D99D67E2DF8309";
        $encrypt_xml_data =  $this->CI->getfunctions->encryptt($xml, $key);
        //echo "<pre>".htmlentities(decryptt($encrypt_xml_data, $key)).'<br><br>';
        
        $data['accessCode'] = "AVQQ81XX06RT98DLVC";
        $data['requestId'] = $requestId;
        $data['encRequest'] = $encrypt_xml_data;
        $data['ver'] = "1.0";
        $data['instituteId'] = "AC62";
        //echo $data['requestId'].'<br>';
        $parameters = http_build_query($data);
        
        $url = "https://api.billavenue.com/billpay/extComplaints/track/xml";
        //echo '<br>'.$encrypt_xml_data;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        //echo '<br><br>Encrypted Response: ' . $result . "<br><br>";
        $response =  $this->decryptt($result, $key);
        return $response;
    }


    public function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP from shared internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP passed from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Regular IP
            return $_SERVER['REMOTE_ADDR'];
        }
    }

        /**
         * base64_encode function
         * @param data
         */
        function base64url_encode($data)
        {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        /**
         * Create JWT Token
         */
        function create_jwt_token()
        {
            //build the headers
            $headers = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
            $headers_encoded = $this->base64url_encode($headers);

            //build the payload
            $payload = json_encode(['timestamp' => time(), 'partnerId' => Constant::PARTNERID, 'reqid' => time() . rand(111,999)]);
            $payload_encoded = $this->base64url_encode($payload);

            //build the signature
            $key = Constant::JWTKEY;
            $signature = hash_hmac('sha256', $headers_encoded . "." . $payload_encoded, $key, true);
            $signature_encoded = $this->base64url_encode($signature);

            //build and return the token
            $token = "$headers_encoded.$payload_encoded.$signature_encoded";

            return $token;
        }
        /**
         * Create JWT Token
         */
        function create_jwt_token_other()
        {
            //build the headers
            $headers = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
            $headers_encoded = $this->base64url_encode($headers);

            //build the payload
            $payload = json_encode(['timestamp' => time(), 'partnerId' => Constant::PARTNERID, 'reqid' => time() . rand(0,9)]);
            $payload_encoded = $this->base64url_encode($payload);

            //build the signature
             $key = Constant::JWTKEY;
            $signature = hash_hmac('sha256', $headers_encoded . "." . $payload_encoded, $key, true);
            $signature_encoded = $this->base64url_encode($signature);

            //build and return the token
            $token = "$headers_encoded.$payload_encoded.$signature_encoded";

            return $token;
        }
   
     function callApi($method, $url, $data = [], $headers = []){

           

            $curl = curl_init();

            switch (strtoupper($method)) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;

                case "GET":
                    if (!empty($data)) {
                        $url .= '?' . http_build_query($data);
                    }
                    break;

                default:
                    throw new Exception("Unsupported request method: $method");
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            if (!empty($headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                return json_encode(['success' => false, 'error' => $error], true);
            }

            return json_encode(['success' => true, 'response' => $response], true);
        }


        function isJson($string) {
        if (!is_string($string)) return false;

        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
        }
        
    public static function sanitizeInput($data) {
      // Trim whitespace from beginning and end
      $data = trim($data);

      // Remove backslashes
      $data = stripslashes($data);

      // Convert special characters to HTML entities (to prevent XSS)
      $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

      return $data;
  }


 public static function  sanitizeEmail($email) {
      return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
  }

 public static function validEmail($email) {
      return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
  }

public static  function sanitizeURL($url) {
      return filter_var(trim($url), FILTER_SANITIZE_URL);
  }

public static  function sanitizeInt($number) {
      return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
  }


public static  function maskAadharNumber($number) {
      // Keep only digits
      $cleanNumber = preg_replace('/\D/', '', $number);
      
      // Check length and mask all but the last 4 digits
      $length = strlen($cleanNumber);
      if ($length <= 4) {
          return str_repeat('X', $length); // Too short, just mask everything
      }
      
      $maskedPart = str_repeat('X', $length - 4);
      $visiblePart = substr($cleanNumber, -4);
      
      return $maskedPart . $visiblePart;
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

 $cfO =  CommonFunction::getInstance();