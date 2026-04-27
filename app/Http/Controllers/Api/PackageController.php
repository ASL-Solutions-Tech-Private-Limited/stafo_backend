<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Package;
use App\Models\PaymentInfo;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use App\Models\PackageFeature;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class PackageController extends Controller
{
   
    public function package()
    {
        try {
            
            $packages = Package::with('features')->where('status','active')->get();

            return response()->json([
                'status' => true,
                'message' => 'Packages fetched successfully',
                'data' => $packages
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function packageFeature($package_id)
    {
        try {
            $company_id = Auth::id(); 
            $packagefeature = PackageFeature::with('features')->where('package_id', $package_id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Package features fetched successfully',
                'data' => $packagefeature
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function hasgenerate(Request $request){

        $company = CompanyDetail::find($request->company_id);
        $package = Package::find($request->package_id); // Assuming you have the package ID from the request
        $merchantKey = '1AJhSD'; // Replace with your merchant key
        $salt = 'tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq'; // Replace with your salt key

        // Collect user inputs from the form
        $firstname = $company->company_name;  // Example first name
        $email = $company->email;
        $amount = $package->discount_price ? $package->discount_price : $package->price;  // Amount to be charged
        $phone = $company->mobile_no;  // User's phone number
        $productinfo = $package->package_name;  // Example product info
        $txnid = uniqid();  // Transaction ID
        $duration = $package->days;  // Duration in days
        $user_id = $company->id;  // User ID
        $package_id = $package->id;
        // Prepare the hash string for PayU
        $hash_string = $merchantKey . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email .'|'.$user_id.'|'.$duration.'|'.$package_id.'|||||||{"billingAmount": "'.$amount.'","billingCurrency": "INR","billingCycle": "WEEKLY","billingInterval": 1,"paymentStartDate": "2025-09-28","paymentEndDate": "2025-10-14"}|' . $salt;

        // Generate the hash using SHA-512
        $hash = hash('sha512', $hash_string);

        $hashArray['merchantKey'] = $merchantKey;
        $hashArray['salt'] = $salt;
        $hashArray['txnid'] = $txnid;
        $hashArray['amount'] = $amount;
        $hashArray['productinfo'] = $productinfo;
        $hashArray['firstname'] = $firstname;
        $hashArray['email'] = $email;
        $hashArray['phone'] = $phone;
        $hashArray['duration'] = $duration;
        $hashArray['user_id'] = $user_id;
        $hashArray['package_id'] = $package_id;

        return response()->json([
            'status' => true,
            'message' => 'Hastag generated successfully',
            'hash' => $hash,
            'hashParam' => $hashArray,
        ], 200);
    }

    public function success(Request $request){
        $userId = $request->udf1;
        $duration = $request->udf2;
        $packageId = $request->udf3;

        $company = CompanyDetail::find($userId);
        $company->package_id = $packageId;
        $company->package_price = $request->amount;
        $company->subscription_start = date('Y-m-d');
        $company->subscription_end = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
        $company->save();

        PaymentInfo::create([
            'company_id' => $userId,
            'transaction_id' => $request->txnid,
            'payment_status' => $request->status,
            'package_id' => $packageId,
            'payment_amount' => $request->amount,
            //'message' => $request->error_Message,
            'payment_info' => json_encode($request->all())
        ]);
        

        $package = Package::find($packageId);
        $subscription = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );

        return response()->json([
            'status' => true,
            'message' => 'Payment successfully processed',
            //'package' => $package,
            'subscription' => $subscription,
        ], 200);
    }

    public function failure(Request $request){
        
        PaymentInfo::create([
            'company_id' => $request->udf1,
            'transaction_id' => $request->txnid,
            'payment_status' => $request->status,
            'package_id' => $request->udf3,
            'payment_amount' => $request->amount,
            'message' => $request->error_Message,
            'payment_info' => json_encode($request->all())
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Payment failed',
            'error' =>$request->error_Message,
            'payment_info' => json_encode($request->all())
        ], 200);
    }

    public function paymentUpdate(Request $request){
        $company_id = $request->company_id;
        $duration = $request->duration;
        $packageId = $request->packageId;

        $expire_date = '';
        if($request->status == 'success'){
            $company = CompanyDetail::find($company_id);
            $company->package_id = $packageId;
            $company->package_price = $request->amount;
            $company->subscription_start = date('Y-m-d');
            $company->subscription_end = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
            $company->save();
            $expire_date = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
        }
        

        PaymentInfo::create([
            'company_id' => $company_id,
            'transaction_id' => $request->txnid,
            'payment_status' => $request->status,
            'package_id' => $packageId,
            'payment_amount' => $request->amount,
            'message' => $request->payment_Message,
            'payment_info' => json_encode($request->payment_info)
        ]);
        

        $package = Package::find($packageId);
        

        return response()->json([
            'status' => true,
            'message' => 'Payment successfully processed',
            //'package' => $package,
            'expire_date' => $expire_date,
        ], 200);
    }

    public function subscriptionInfo(Request $request){
        $company_id = Auth::id(); 
        $subscription = CompanyDetail::with('package')->where('id', $company_id)->select('id','company_name','package_id','package_price','subscription_start','subscription_end')->first();
        $payment = PaymentInfo::where('company_id', $company_id)->orderBy('id', 'desc')->first();
        $data = [
            'subscription' => $subscription,
            'payment' => $payment
        ];
       
        $filename = 'invoice_' . time() . '.pdf';
        $pdf = Pdf::loadView('user.packages.invoice', $data)->setPaper('a4', 'portrait');
        $pdfPath = public_path('uploads/packages/' . $filename);

        // Make sure directory exists
        if (!File::exists(public_path('uploads/packages'))) {
            File::makeDirectory(public_path('uploads/packages'), 0755, true);
        }

        file_put_contents($pdfPath, $pdf->output());

        $downloadUrl = asset('uploads/packages/' . $filename);

        return response()->json([
            'status' => true,
            'message' => 'Subscription info fetched successfully',
            'data' => $subscription,
            'downloadUrl' => $downloadUrl
        ], 200);
    }

    public function invoicePDF(Request $request)
    {
        $company = CompanyDetail::find($request->company_id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid company_id. Company not found.',
            ], 200);
        }

        $employee_id = $request->employee_id;

        $month = $request->month;
        $year = $request->year;

        $monthArray = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $employeeSalaries = EmployeeSalary::where('company_id', $company->id)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $employee_id)
            ->get();

        $employee = Employee::with(['department', 'city', 'bankAccount'])
            ->find($employee_id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid employee_id. Employee not found.',
            ], 201);
        }

        $salaryGroups = [];
        $salaryTotals = [];

        foreach ($employeeSalaries as $salary) {
            $type = $salary->salaryType->salary_type ?? 'Others';
            $label = $salary->label ?? $salary->salaryType->name;

            $entry = [
                'label' => $label,
                'amount' => (float) $salary->amount,
            ];

            if (!isset($salaryGroups[$type])) {
                $salaryGroups[$type] = [];
                $salaryTotals[$type] = 0;
            }

            $salaryGroups[$type][] = $entry;
            $salaryTotals[$type] += $entry['amount'];
        }

        $data = [
            'monthArray' => $monthArray,
            'salaryMonth' => $month,
            'salaryYear' => $year,
            'employeeSalaries' => $employeeSalaries,
            'company' => $company,
            'employee' => $employee,
            'salaryGroups' => $salaryGroups,
            'salaryTotals' => $salaryTotals,
        ];

        $filename = 'salary_' . $month . '_' . $year . '_' . time() . '.pdf';
        $pdf = Pdf::loadView('user.salarytype.salary_pdf', $data)->setPaper('a4', 'portrait');
        $pdfPath = public_path('uploads/salary_slips/' . $filename);

        // Make sure directory exists
        if (!File::exists(public_path('uploads/salary_slips'))) {
            File::makeDirectory(public_path('uploads/salary_slips'), 0755, true);
        }

        file_put_contents($pdfPath, $pdf->output());

        $downloadUrl = asset('uploads/salary_slips/' . $filename);

        return response()->json([
            'success' => true,
            'message' => 'Salary slip generated successfully.',
            'data' => [
                'filename' => $filename,
                'download_url' => $downloadUrl
            ]
        ], 200);
    }


}