<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\BbpsServiceCategory;
use App\Models\DcCcBbpsCategory;
use App\Models\DcCcBbpsSubCategory;
use App\Models\Circle;
use App\Models\BbpsOperatorMaster;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;
use App\Library\CommonFunction;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Library\Constant;
use App\Models\BillPayment;
use App\Models\BbpsCommission;

use App\Models\WalletHistory;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyDetail;
use App\Models\Employee;


class BillPayController extends Controller
{
   use ApiResponse;
   /** 
   * function / API is to get Biller Category
   * @param $request
   * @return json object
   * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
   * 
   * 
   * */
   public function getBillerCategory(Request $request){

    try {
       $serviceCategory = BbpsServiceCategory::select(['name','category_icon','name as category_code'])->where(['status'=>1])->orderBy('orderBy', 'asc')->get();
       return $this->success($serviceCategory, "Biller Category List");   
    } catch ( Exception $e ) { 
    
       return $this->error($e->getMessage());  
    }

   }

   /** 
   * function / API is to get Biller / Opertaor sub category
   * @param $request
   * @return json object
   * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
   * 
   * 
   * */
   public function getBillerSubCategory(Request $request){

    try {
          $request = $request->all();
          $billerCategory = $request['category'] ?? '';  

          if(empty($billerCategory)){
            throw new Exception ( 'Please choose biller category' );
          }

        $billerCategory = CommonFunction::sanitizeInput($billerCategory);
        $serviceCategory = DcCcBbpsSubCategory::select(['name','category','code'])->where(['status'=>1, 'category'=>$billerCategory])->orderBy('order_by', 'asc')->get();
       return $this->success($serviceCategory, "Biller Sub Category List");   
    } catch ( Exception $e ) { 
    
       return $this->error($e->getMessage());  
    }

   }



   /** 
   * function / API is to get Biller / Operator List
   * @param $request
   * @return json object
   * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
   * 
   * 
   * */
   public function getBiller(Request $request){

    try {

       $request = $request->all();
       $billerCategory = $request['category'] ?? ''; 
       $circle = $request['circle'] ?? '';  
       if(empty($billerCategory)){
         throw new Exception ( 'Please choose biller category');
       }

       $whereCond = [ 'status' => 1, 'category' => $billerCategory ];
       if(!empty($circle)){
         $wherCond['blr_coverage'] = $circle;
       }

       $billerCategory = CommonFunction::sanitizeInput($billerCategory);
       $serviceCategory = BbpsOperatorMaster::select(['name','operator_code','service','category'])->where($whereCond)->orderBy('name', 'asc')->get();
       return $this->success($serviceCategory, "Biller List");   
    } catch ( Exception $e ) { 
    
       return $this->error($e->getMessage());  
    }

   }

  /** 
   * function / API is to get circle list
   * @param $request
   * @return json object
   * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
   * 
   * 
   * */
   public function getBBPSCircle(Request $request){

    try {
       $circle = Circle::all();
       return $this->success($circle, "Circle List");   
    } catch ( Exception $e ) { 
    
       return $this->error($e->getMessage());  
    }

   }


 /** 
  * Function is get biller / operator detail 
  * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
  * @param $request
  * @return JSON object 
  * 
  * 
  * */

public function getBillerDetail(Request $request){
      try {
        
         $request = $request->all();
         $operatorCode = $request['operator_code'] ?? '';  

         if(empty($operatorCode)){
               throw new Exception ( 'Please choose biller / operator');
         }
         $operatorCode = CommonFunction::sanitizeInput($operatorCode);
         $refId = CommonFunction::generateRandomString(); 
         $billerDetail = BbpsOperatorMaster::where(['operator_code' => $operatorCode])->get()->toArray();
                    
         if(!empty($billerDetail[0]['message'])){
               $mdmRequestNew=$billerDetail[0]['message'];
         }else{
               $cfO = CommonFunction::getInstance();
               $mdmRequestNew = $cfO->mdmRequestNew($operatorCode, $refId);
               $resp2_arr = json_decode($mdmRequestNew, true);
               if($resp2_arr['responseCode']=='000'){
               BbpsOperatorMaster::where('operator_code', $operatorCode)->update(['message' => $mdmRequestNew]);
         }
         }
         $resp2_arr = json_decode($mdmRequestNew, true);
         $msg='';
         $status=0;
       if($resp2_arr['responseCode']=='000'){
          $paramInfo = $resp2_arr['biller']['billerInputParams']['paramInfo']; 
           if(!$this->isMultiDimensional($paramInfo)){
               $resp2_arr['biller']['billerInputParams']['paramInfo'] = [$paramInfo];  
           }

         $status=1;
         $msg = "Success";
         $data = ["refid" => $refId,"message"=>$msg, "mdmRequestNew"=>$resp2_arr];
         return $this->success($data, "Biller Detail By Operator code"); 
      
       } else {
            throw new Exception ($resp2_arr['errorInfo']['error']['errorMessage']);
       }
      } catch ( Exception $e) {
        return $this->error($e->getMessage());  
      }

}

/** 
 * Function is to check coming array is multi / dimension    
 * @param array type value
 * @return boolean
 * 
 * */

private function isMultiDimensional(array $array): bool {
    return count(array_filter($array, 'is_array')) > 0;
}
/** 
  * Function is fetch bill
  * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
  * @param $request
  * @return JSON object 
  * 
  * 
  * */

public function fetchBill(Request $request)
{
    try {
        $cfO = CommonFunction::getInstance();

        $requestData = $request->all();
        $category = $requestData['category'] ?? '';
        $operatorCode = $requestData['operator_code'] ?? '';

        if (empty($operatorCode)) {
            throw new Exception('Please choose biller / operator');
        }

        if (empty($category)) {
            throw new Exception('Biller category is missing.');
        }

        $postdata = $requestData['postdata'] ?? [];
        $refId = CommonFunction::generateRandomString();
        $blno = $operatorCode;

        if (!empty($blno)) {
            $msg = '';
            $response = [];
            $response['status'] = 0;
            $inpparm = '';
            $cnt = 0;

            foreach ($postdata as $key => $val) {
                if (!empty($key)) {
                    $cnt++;
                    $key = str_replace("_", " ", $key);
                    $inpparm .= '<input><paramName>' . htmlspecialchars($key) . '</paramName><paramValue>' . htmlspecialchars($val) . '</paramValue></input>';
                    if ($cnt == 1) {
                        $msg = '<input type="hidden" value="' . htmlspecialchars($val) . '" name="ccanumber" id="ccanumber" required>';
                    }
                }
            }

            $ip = $cfO->getUserIP();
            $xml1 = '<?xml version="1.0" encoding="UTF-8"?><billFetchRequest><agentId>CC01AC62AGTU00000002</agentId><agentDeviceInfo><ip>' . $ip . '</ip><initChannel>AGT</initChannel><mac>01-23-45-67-89-ab</mac></agentDeviceInfo><customerInfo><customerMobile>9889876543</customerMobile><customerEmail /><customerAdhaar /><customerPan /></customerInfo><billerId>' . $blno . '</billerId><inputParams>' . $inpparm . '</inputParams></billFetchRequest>';

            if (env('APP_ENV') != 'local') {
               
                $datarr = $cfO->billFetchRequest($xml1, $refId);
            } 
            else {
                $datarr = '<?xml version="1.0" encoding="UTF-8"?><billFetchResponse> <responseCode>000</responseCode> <paymentRefId>45f7c2fbeadc4e0c9fe9586735e94101</paymentRefId> <inputParams> <input> <paramName>a</paramName> <paramValue>10</paramValue> </input> </inputParams> <billerResponse> <billAmount>100000</billAmount> <billDate>2015-06-14</billDate> <billNumber>12303</billNumber> <billPeriod>june</billPeriod> <customerName>BBPS</customerName> <dueDate>2015-06-20</dueDate> </billerResponse> </billFetchResponse>';
            }

            $xml = simplexml_load_string($datarr, "SimpleXMLElement", LIBXML_NOCDATA);
            if ($xml === false) {
                throw new Exception("Invalid XML response from biller");
            }

            // Safe XML extraction
            $billerResponseXml = isset($xml->billerResponse) ? $xml->billerResponse->asXML() : '';
            $additionalInfoXml = isset($xml->additionalInfo) ? $xml->additionalInfo->asXML() : '';

            $datar = json_encode($xml);
            $resp2_arr = json_decode($datar, true);

            if (isset($resp2_arr['responseCode']) && $resp2_arr['responseCode'] == '000') {
                
                // Fetch paymentRefId from response (or fallback to generated $refId)
                $paymentRefId = $resp2_arr['paymentRefId'] 
                    ?? ($resp2_arr['billerResponse']['paymentRefId'] 
                    ?? ($resp2_arr['approvalRefNum'] ?? $refId));

                // 1. Normalize additionalInfo (Always return array to Mobile Developer)
                $adinfo = '';
                if (!isset($resp2_arr['additionalInfo']) || empty($resp2_arr['additionalInfo'])) {
                    $resp2_arr['additionalInfo'] = [
                        'info' => []
                    ];
                } else {
                    $infoData = $resp2_arr['additionalInfo']['info'] ?? [];
                    if (!empty($infoData) && isset($infoData['infoName'])) {
                        $infoData = [$infoData];
                        $resp2_arr['additionalInfo']['info'] = $infoData;
                    }

                    if (is_array($infoData)) {
                        foreach ($infoData as $inprmm) {
                            if (isset($inprmm['infoName']) && isset($inprmm['infoValue'])) {
                                $adinfo .= '<info><infoName>' . htmlspecialchars($inprmm['infoName']) . '</infoName><infoValue>' . htmlspecialchars($inprmm['infoValue']) . '</infoValue></info>';
                            }
                        }
                    }
                }

                // 2. Normalize inputParams
                $inpparmXml = '';
                if (!isset($resp2_arr['inputParams']) || empty($resp2_arr['inputParams'])) {
                    $resp2_arr['inputParams'] = [
                        'input' => []
                    ];
                } else {
                    $inputParamss = $resp2_arr['inputParams']['input'] ?? [];
                    if (!empty($inputParamss) && isset($inputParamss['paramName'])) {
                        $inputParamss = [$inputParamss];
                        $resp2_arr['inputParams']['input'] = $inputParamss;
                    }

                    if (is_array($inputParamss)) {
                        foreach ($inputParamss as $inprm) {
                            if (isset($inprm['paramName']) && isset($inprm['paramValue'])) {
                                $inpparmXml .= '<input><paramName>' . htmlspecialchars($inprm['paramName']) . '</paramName><paramValue>' . htmlspecialchars($inprm['paramValue']) . '</paramValue></input>';
                            }
                        }
                    }
                }

                // 3. Normalize billerResponse & Amount Options
                $paramInfo = $resp2_arr['billerResponse'] ?? [];
                $billAmount = $paramInfo['billAmount'] ?? 0;
                $customerName = $paramInfo['customerName'] ?? '';
                $billDate = $paramInfo['billDate'] ?? null;
                $billNumber = $paramInfo['billNumber'] ?? null;
                $dueDate = $paramInfo['dueDate'] ?? null;
                $billPeriod = $paramInfo['billPeriod'] ?? null;

                // Normalize amountOptions if present
                if (isset($paramInfo['amountOptions']['option'])) {
                    $options = $paramInfo['amountOptions']['option'];
                    if (isset($options['amountName'])) {
                        $resp2_arr['billerResponse']['amountOptions']['option'] = [$options];
                    }
                }

                $billAmountt = 'billAmountt';
                if ($category == 'Fastag' || $category == 'Loan Repayment') {
                    $msg .= '<div class="col-xl-12 col-lg-6 col-md-6"><div class="form-group has-cst"><input class="form-control" type="number" value="' . round(($billAmount / 100), 2) . '" name="amount" id="amount" required placeholder="Enter Amount "><span class="bar"></span><label for="amount" class="required"> Enter Amount<label></div></div>';
                }

                // Build paymentRefId XML node
                $paymentRefIdXml = '<paymentRefId>' . htmlspecialchars($paymentRefId) . '</paymentRefId>';

                // Updated billPaymentRequest XML with paymentRefId
                $xmldataa = '<?xml version="1.0" encoding="UTF-8"?><billPaymentRequest><agentId>CC01AC62AGTU00000002</agentId><agentDeviceInfo><ip>' . $ip . '</ip><initChannel>AGT</initChannel><mac>01-23-45-67-89-ab</mac></agentDeviceInfo><customerInfo><customerMobile>9889876543</customerMobile><customerEmail></customerEmail><customerAdhaar></customerAdhaar><customerPan></customerPan></customerInfo><billerId>' . $blno . '</billerId><inputParams>' . $inpparmXml . '</inputParams>' . $billerResponseXml . $additionalInfoXml . $paymentRefIdXml . '<amountInfo><amount>' . $billAmountt . '</amount><currency>356</currency><custConvFee>0</custConvFee><amountTags></amountTags></amountInfo><paymentMethod><paymentMode>Cash</paymentMode><quickPay>N</quickPay><splitPay>N</splitPay></paymentMethod><paymentInfo><info><infoName>Remarks</infoName><infoValue>Received</infoValue></info></paymentInfo></billPaymentRequest>';

                $response['status'] = 1;
                $response['xml'] = base64_encode(base64_encode($xmldataa));
                $response['datar'] = base64_encode($datarr);
                $response['message'] = 'Bill Detail is Successfull';
                $response['billData'] = $resp2_arr;
                $response['refid'] = $refId;
                $response['paymentRefId'] = $paymentRefId;

                return $this->success($response, "Biller Detail By Operator code");

            } else {
                $errorMsg = $resp2_arr['errorInfo']['error']['errorMessage'] ?? ($resp2_arr['message'] ?? 'Failed to fetch bill');
                throw new Exception($errorMsg);
            }
        }

    } catch (Exception $e) {
        return $this->error($e->getMessage());
    }
}



/** 
  * Function is pay bill
  * @author Prabhat Kumar Tiwari<ASL Tech Solutions Pvt. Ltd. >
  * @param $request
  * @return JSON object 
  * 
  * 
  * */

  public function initiateBillPay(Request $request){
   try {
         $cfO = CommonFunction::getInstance();
         $userId = Auth::id();

       
         $requestData = $request->all();
         $postdata = $requestData['postdata'] ?? [];
         $userType = CommonFunction::sanitizeInput($requestData['user_type'] ?? '');
         $ref_id = CommonFunction::sanitizeInput($postdata['requestId'] ?? '');
         $txnAmt = (float)CommonFunction::sanitizeInput($postdata['amount'] ?? 0);
         $operatorCode = CommonFunction::sanitizeInput($requestData['operator_code'] ?? '');
         $category = $requestData['category'] ?? '';
         $mode = $postdata['mode'] ?? 'online';
         $bill_fetch = $postdata['billPaymentRequestxml'] ?? '';      
         $txnId = $this->generateTxnId();

         // 1. Resolve BBPS Service Category using only BbpsServiceCategory model
         $serviceCategory = BbpsServiceCategory::where('name', $category)->first();

         // 2. Resolve ID based on user_type (only Employee and CompanyDetail)
         $companyId = null;
         if ($userType === 'employee') {
             $employee = Employee::find($userId);
             $companyId = $employee->id ?? $userId;
         } elseif ($userType === 'company') {
             $company = CompanyDetail::find($userId);
             $companyId = $company->id ?? $userId;
         }

         // 3. Fetch BBPS Commission Rule using only BbpsCommission model
         $commissionRule = null;
         if ($companyId && $serviceCategory) {
             $commissionRule = BbpsCommission::where('company_id', $companyId)
                 ->where('billtypeid', $serviceCategory->id)
                 ->where('status', '1')
                 ->first();

             // Fallback for employee if commission is configured under company_id
             if (!$commissionRule && $userType === 'employee' && !empty($employee->company_id)) {
                 $commissionRule = BbpsCommission::where('company_id', $employee->company_id)
                     ->where('billtypeid', $serviceCategory->id)
                     ->where('status', '1')
                     ->first();
             }
         }

         // 4. Resolve Commission / Surcharge rates and type
         $comper = 0.0;
         $gstper = 0.0;
         $isFlat = false;
         $isSurcharge = false;

         if ($commissionRule) {
             $comper = (float)($commissionRule->comper ?? 0);
             $gstper = (float)($commissionRule->gstper ?? 0);
             $isFlat = ($commissionRule->isflat == '1');
             $isSurcharge = ($commissionRule->issurcharge == '1');
         } elseif ($serviceCategory && $serviceCategory->commission !== null) {
             $comper = (float)$serviceCategory->commission;
             $gstper = 0.0;
             $isFlat = ($serviceCategory->isFlat == '1');
             $isSurcharge = false;
         }

         // 5. Calculate Commission, GST and Deductible/Payable Amount
         $commissionAmount = 0.0;
         $gstAmount = 0.0;

         if ($comper > 0) {
             if ($isFlat) {
                 $commissionAmount = round($comper, 2);
             } else {
                 $commissionAmount = round(($txnAmt * $comper) / 100, 2);
             }

             if ($gstper > 0 && $commissionAmount > 0) {
                 $gstAmount = round(($commissionAmount * $gstper) / 100, 2);
             }
         }

         if ($isSurcharge) {
             // Surcharge increases the payable amount
             $totalSurcharge = round($commissionAmount + $gstAmount, 2);
             $cashback = 0.0;
             $deductibleAmount = round($txnAmt + $totalSurcharge, 2);
         } else {
             // Commission reduces deductible amount / serves as cashback
             $netCommission = round(max(0, $commissionAmount - $gstAmount), 2);
             $cashback = $netCommission;
             $deductibleAmount = round(max(0, $txnAmt - $netCommission), 2);
         }

         $product_info = "Bill Payment - " . $category . ' - Amount ' . $deductibleAmount;
         $initialRequest = [
               'user_id' => $userId,
               'user_type' => $userType,
               'txnid' => $txnId,
               'amount' => $deductibleAmount,
               'bill_amount' => $txnAmt,
               'commission' => $commissionAmount,
               'product_info' => $product_info,
               'status'=> 'pending'
         ];
         $calculatedHash = $this->generatePayUHash($initialRequest, $userId, $userType);
         $initialRequest['hash_calculated'] = $calculatedHash;
         
         $transactionObj = new Transaction;
         $initialTransactionId =  $transactionObj->transaction_insert($initialRequest); 
         $paymentSDKRequest = [];
         if($initialTransactionId){
           $paymentSDKRequest = [
               'user_id' => $userId ?? 123,
               'user_type' => $userType,
               'txnid' => $txnId,
               'amount' => $deductibleAmount,
               'bill_amount' => $txnAmt,
               'deductibleAmount' => $deductibleAmount,
               'product_info' => $product_info,
               'BillType' => $category,
               'mode' => $mode,
               'operator_code' => $operatorCode,
               'billFetchPost' => $bill_fetch,
               'requestId' => $ref_id,
               'cashBack' => $cashback,
               'commission' => $comper,
               'commission_amount' => $commissionAmount,
               'gst_percentage' => $gstper,
               'gst_amount' => $gstAmount,
               'is_surcharge' => $isSurcharge ? 1 : 0,
               'is_flat' => $isFlat ? 1 : 0,
               'hash_calculated' => $calculatedHash
           ]; 
         
         }else{
            throw new Exception ('Something went wrong.');
         }  
   return $this->success($paymentSDKRequest, "Payment Request");        
   } catch ( Exception $e ) {
      return $this->error($e->getMessage());  
   }

  }


/** 
 * Function is to check payment status and pay bill
 * @author Prabhat Kumar Tiwari<prabhatji@aslwallets.in>
 * @param $request
 * @return $response
 * 
 * 
 * */

public function checkPaymentStatusAndPayBill(Request $request){

         try {
         $cfO = CommonFunction::getInstance();
         $userId = Auth::id();
         $request = $request->all();
         $txnId = $request['txnId'];
         $txnResponse = $request['transactionResponse'];
         $processBillPay = 0;
         DB::beginTransaction();
         $order = Transaction::where('txnid', $txnId)->lockForUpdate()->first();

         if (!$order) {
                throw new \Exception("Order not found for txnid: $txnId");
            }
         $updateRequest = [
                  "payu_payment_id" => $txnResponse['paymentId'] ?? $txnResponse['id'],
                  "hash_received" => $txnResponse['hash'],
                  "payu_response" => json_encode($txnResponse, true),
                  "updated_at" => date("Y-m-d H:i:s")
            ];

         if($txnResponse['status'] == 'success'){
             $processBillPay = 1;
             $updateRequest['status'] = $txnResponse['status'];
        
         } elseif($txnResponse['status'] == 'failure'){
             $updateRequest['status'] = 'failed';

         } elseif($txnResponse['status'] == 'process'){
            $updateRequest['status'] = 'process';
         }
       $transactionObj = new Transaction;  
       $transactionObj->update_transaction($updateRequest, $order->id);   
       DB::commit();
        $billPayResponse = $this->processBillPayment($request);
        
       if($processBillPay == 1){
        $response =  $billPayResponse = $this->processBillPayment($request);

               
       }
       return $this->success($response, 'Bill Pay Successfully'); 
      } catch ( Exception $e ){
             DB::commit();
         return $this->error($e->getMessage()); 
      }
  
}

 private function generateTxnId() {
    return substr(hash('sha256', uniqid(mt_rand(), true)), 0, 20);
}




private function processBillPayment($request){
  try {

         $cfO = CommonFunction::getInstance();
         $userId = Auth::id() ?? 123 ;
         $userType =  CommonFunction::sanitizeInput($request['user_type']);
         $txnId =  CommonFunction::sanitizeInput($request['txnId']);
         $postData = $request['postdata'];
         $bill_fetch =base64_decode(base64_decode($postData ['billPaymentRequestxml']));
         $ref_id = CommonFunction::sanitizeInput($postData ['requestId']);
         $txnAmt = CommonFunction::sanitizeInput($postData ['amount']);
         $bill_fetch=str_replace("billAmountt",($txnAmt*100),$bill_fetch);
         $ca_number =  CommonFunction::sanitizeInput($request['operator_code']);
         $operator_code =  CommonFunction::sanitizeInput($request['operator_code']);
         $category =  CommonFunction::sanitizeInput($request['category']);
         $data = $this->convertXMLToJson($bill_fetch);
         
         $data = json_decode($data, true);
        
         $inputs = $data['inputParams']['input'];
         if (isset($inputs['paramName'])) {
          $inputs = [$inputs];
         }
         $mode = $postData['mode'];
         $c = 0;
         $consumerId = "";
         foreach ($inputs as $input) {
            if($c == 0){
               $consumerid = $input['paramValue'];
               break;
            }
            $c++;  
          }
           
         DB::beginTransaction();  
         $insertData = [
         "channel_id"            =>      Constant::CHANNEL_ID, 
         "user_id"               =>      $userId,
         "user_type"             =>      $userType,
         "category"              =>      $category,
         "operator_id"           =>      $operator_code,
         "ca_number"             =>      $consumerId,
         "amount"                =>      $txnAmt,
         "mode"                  =>      $mode,
         "bill_fetch_post"       =>      $bill_fetch,
         "reference_id"          =>      $ref_id,
         "paymentTxnId "         =>      $txnId,
         "APISwitching"          =>      'Billavenue',
         "status"                =>      2,
         "wallet_debited"        =>      1,
         "created_at"            =>      date("Y-m-d H:i:s"),
         ];

/** Initiate the transaction */  
  $billPaymentObj =  new BillPayment;  
  $billPaymentId =  $billPaymentObj->transaction_insert($insertData);
 
    if($billPaymentId) {
       if(env('APP_ENV') != 'local'){

              $res = $cfO->billPayRequest($bill_fetch,$ref_id); 
              } else {

         $res='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><ExtBillPayResponse><responseCode>000</responseCode><responseReason>Successful</responseReason><txnRefId>CC013094BAAE00035053</txnRefId><requestId>OINBEPVNFA6J25XA145NM05IV3LV7KE3OMV</requestId><approvalRefNumber>12345037</approvalRefNumber><txnRespType>FORWARD TYPE RESPONSE</txnRespType><inputParams><input><paramName>a</paramName><paramValue>10</paramValue></input><input><paramName>a b</paramName><paramValue>20</paramValue></input><input><paramName>a b c</paramName><paramValue>30</paramValue></input><input><paramName>a b c d</paramName><paramValue>40</paramValue></input><input><paramName>a b c d e</paramName><paramValue>50</paramValue></input></inputParams><CustConvFee>0</CustConvFee><RespAmount>100000</RespAmount><RespBillDate>2016-07-01</RespBillDate><RespBillNumber>12303037</RespBillNumber><RespBillPeriod>Jul</RespBillPeriod><RespCustomerName>Ashish</RespCustomerName><RespDueDate>2016-07-30</RespDueDate></ExtBillPayResponse>'; 
      }

     $data = $this->convertXMLToJson($res);
     $api_response = json_decode($data, true);
     $resStatusMsg=$api_response['responseReason'];
     $apiTrnID=$api_response["txnRefId"];
     $resApiTxnId=$api_response["approvalRefNumber"];

      if($api_response['responseCode']=='000'){
         $uDataDC = [
          "status" => 1 ,
          "updated_at" => date("Y-m-d H:i:s")
         ];
     
      $updateTransactionDC = $billPaymentObj->update_transaction($uDataDC, $ref_id);

      } elseif($api_response['responseReason']=='Failure'){
         $resStatus='Failed';
         $resStatus='Failed';
          $uDataDC = [
          "status" => 1 ,
          "updated_at" => date("Y-m-d H:i:s")
         ];
         $updateTransactionDC = $billPaymentObj->update_transaction($uDataDC, $ref_id);

         $modelMap = [
          'employee' => 'App\Models\Employee',
          'company' => 'App\Models\CompanyDetail',
        ];

        $modalName = $modelMap[$userType] ?? null;
        if(empty($modalName)){
         throw new Exception ("Unknown User Type");
        }

        $userDetail =  $modalName::where('id', $userId)->lockForUpdate()->first();

        if(!$userDetail){
          throw new Exception ('Unknow user'); 
        }
        
        $openingBalance = $userDetail->wallet;
        $closingBalance = $openingBalance + $txnAmt;
        $modalName::where('id', $userId)->update(['wallet'=>$closingBalance]);
        $wallet_history = [
            'user_id' => $userId,
            'user_type' => $userType,
            'openingBalance' => $openingBalance,
            'amount' => $amount,
            'closing_balance' => $closingBalance,
            'transaction_type' => 'credit',
            'description' => 'Amount credited',
            'reference_id' => $ref_id
        ];
       $walletHistoryObj = new WalletHistory;
       $wallet_history_id = $walletHistoryObj->transaction_insert($wallet_history);
       if($wallet_history_id){
         throw new Exception ("Unable to update wallet history");
       }
        
      }
   
   }else{
      throw new Exception("Oops! Unable to initiate transaction."); 
     } 
    db::commit(); 

    $response = [
            'status' => 200,
            'apiTrnID' => $apiTrnID,
            'resApiTxnId' => $resApiTxnId,
            'id'   => base64_encode($billPaymentId),
            'txnstatus' => $resStatusMsg,
            'message' => $resStatusMsg,
            'ref_id' => $ref_id,
        ];
     return $response;
   } catch ( Exception $e ){
      db::commit();  
     return $this->error($e->getMessage()); 
    }
 
}


private function convertXMLToJson($xmlString){
   $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
   return json_encode($xml);
} 
/** 
 * Function is to generate hash for payU
 * @author Prabhat Kumar Tiwari<prabhatji@aslwallets.in>
 * @param $request
 * @return $response
 * 
 * 
 * */

private function generatePayUHash($request, $userId, $userType)
{
         $modelMap = [
             'employee' => 'App\Models\Employee',
             'company'  => 'App\Models\CompanyDetail',
             'user'     => 'App\Models\User',
         ];

         $modalName = $modelMap[$userType] ?? null;
         $userDetail = null;
         if(!empty($modalName)){
             $userDetail = $modalName::where('id', $userId)->first();
         }

         if (!$userDetail && $userId) {
             $userDetail = Employee::find($userId) ?? CompanyDetail::find($userId) ?? User::find($userId);
         }

         $key         =  '1AJhSD';
         $salt        = 'tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq';
         $txnid       = $request['txnid'];
         $amount      = $request['amount'];
         $productinfo = $request['product_info'];
         $firstname   = $userDetail->name ?? ($userDetail->company_name ?? 'Customer'); 
         $email       = $userDetail->email ?? 'customer@example.com'; 
         $hash_string = $key . "|" . $txnid . "|" . $amount . "|" . $productinfo . "|" . $firstname . "|" . $email . "|||||||||||" . $salt;
         $hash = strtolower(hash("sha512", $hash_string));
         return $hash;
}

    /**
     * Function / API to get BBPS Transaction Report with summary KPIs, filters & pagination
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bbpsTransactionReport(Request $request)
    {
        try {
            $userId = Auth::id();
            $authUser = Auth::user();

            // Detect user type (employee or company)
            $userType = $request->input('user_type');
            if (empty($userType)) {
                if ($authUser instanceof Employee) {
                    $userType = 'employee';
                } elseif ($authUser instanceof CompanyDetail) {
                    $userType = 'company';
                } else {
                    $isEmployee = Employee::where('id', $userId)->exists();
                    $userType = $isEmployee ? 'employee' : 'company';
                }
            }

            // Input filters
            $search   = $request->input('search');
            $status   = $request->input('status');
            $category = $request->input('category');
            $dateFrom = $request->input('date_from');
            $dateTo   = $request->input('date_to');
            $date     = $request->input('date');
            $perPage  = (int)$request->input('per_page', 15);
            if ($perPage <= 0 || $perPage > 100) {
                $perPage = 15;
            }

            // Base query with relationships
            $query = Transaction::with(['employee.company', 'company'])->latest('id');

            // Scoping based on user role
            if ($userType === 'employee') {
                // Employee views their own transactions
                $query->where('user_id', $userId)->where('user_type', 'employee');
            } elseif ($userType === 'company') {
                // Company views company transactions + employee transactions under this company
                $employeeIds = Employee::where('company_id', $userId)->pluck('id')->toArray();
                $query->where(function ($q) use ($userId, $employeeIds) {
                    $q->where(function ($sub) use ($userId) {
                        $sub->where('user_type', 'company')->where('user_id', $userId);
                    });
                    if (!empty($employeeIds)) {
                        $q->orWhere(function ($sub) use ($employeeIds) {
                            $sub->where('user_type', 'employee')->whereIn('user_id', $employeeIds);
                        });
                    }
                });
            }

            // Search filter (txnid, payu_payment_id, product_info)
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('txnid', 'like', "%{$search}%")
                      ->orWhere('payu_payment_id', 'like', "%{$search}%")
                      ->orWhere('product_info', 'like', "%{$search}%");
                });
            }

            // Status filter
            if (!empty($status) && $status !== 'all') {
                $query->where('status', $status);
            }

            // Category filter
            if (!empty($category) && $category !== 'all') {
                $query->where('product_info', 'like', "%{$category}%");
            }

            // Date filters
            if (!empty($date)) {
                $query->whereDate('created_at', $date);
            } else {
                if (!empty($dateFrom)) {
                    $query->whereDate('created_at', '>=', $dateFrom);
                }
                if (!empty($dateTo)) {
                    $query->whereDate('created_at', '<=', $dateTo);
                }
            }

            // KPI Summary metrics (clone query before pagination)
            $statsQuery = clone $query;
            $totalCount = $statsQuery->count();
            $totalAmount = (float)$statsQuery->sum('amount');
            $totalCommission = (float)$statsQuery->sum('commission');

            $successQuery = (clone $statsQuery)->where('status', 'success');
            $successCount = $successQuery->count();
            $successAmount = (float)$successQuery->sum('amount');

            $pendingQuery = (clone $statsQuery)->where('status', 'pending');
            $pendingCount = $pendingQuery->count();
            $pendingAmount = (float)$pendingQuery->sum('amount');

            $failedQuery = (clone $statsQuery)->where(function ($q) {
                $q->where('status', 'failed')->orWhere('status', 'failure');
            });
            $failedCount = $failedQuery->count();
            $failedAmount = (float)$failedQuery->sum('amount');

            // Paginate Results
            $paginated = $query->paginate($perPage);

            // Transform records
            $transactions = collect($paginated->items())->map(function ($txn) {
                $payerName = '-';
                $companyName = '-';
                if ($txn->user_type === 'employee' && $txn->employee) {
                    $payerName = $txn->employee->name ?? ('Employee #' . $txn->user_id);
                    $companyName = $txn->employee->company->company_name ?? '-';
                } elseif ($txn->user_type === 'company' && $txn->company) {
                    $payerName = $txn->company->company_name ?? ('Company #' . $txn->user_id);
                    $companyName = $txn->company->company_name ?? '-';
                }

                $commVal = (float)($txn->commission ?? 0);
                $billAmt = (float)($txn->bill_amount ?? $txn->amount);

                return [
                    'id'                   => $txn->id,
                    'txnid'                => $txn->txnid,
                    'amount'               => (float)$txn->amount,
                    'formatted_amount'     => '₹' . number_format((float)$txn->amount, 2),
                    'bill_amount'          => $billAmt,
                    'formatted_bill_amount'=> '₹' . number_format($billAmt, 2),
                    'commission'           => $commVal,
                    'formatted_commission' => '₹' . number_format($commVal, 2),
                    'product_info'         => $txn->product_info ?? 'BBPS Payment',
                    'status'               => strtolower($txn->status ?? 'pending'),
                    'payu_payment_id'      => $txn->payu_payment_id,
                    'user_id'              => $txn->user_id,
                    'user_type'            => $txn->user_type,
                    'payer_name'           => $payerName,
                    'company_name'         => $companyName,
                    'created_at'           => $txn->created_at ? $txn->created_at->format('Y-m-d H:i:s') : null,
                    'date'                 => $txn->created_at ? $txn->created_at->format('d M Y') : null,
                    'time'                 => $txn->created_at ? $txn->created_at->format('h:i A') : null,
                ];
            });

            $responseData = [
                'summary' => [
                    'total_count'                 => $totalCount,
                    'total_amount'                => round($totalAmount, 2),
                    'total_amount_formatted'      => '₹' . number_format($totalAmount, 2),
                    'total_commission'            => round($totalCommission, 2),
                    'total_commission_formatted'  => '₹' . number_format($totalCommission, 2),
                    'success_count'               => $successCount,
                    'success_amount'              => round($successAmount, 2),
                    'success_amount_formatted'    => '₹' . number_format($successAmount, 2),
                    'pending_count'               => $pendingCount,
                    'pending_amount'              => round($pendingAmount, 2),
                    'pending_amount_formatted'    => '₹' . number_format($pendingAmount, 2),
                    'failed_count'                => $failedCount,
                    'failed_amount'               => round($failedAmount, 2),
                    'failed_amount_formatted'     => '₹' . number_format($failedAmount, 2),
                ],
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                    'from'         => $paginated->firstItem(),
                    'to'           => $paginated->lastItem(),
                ],
                'transactions' => $transactions,
            ];

            return $this->success($responseData, "BBPS Transaction Report fetched successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}
