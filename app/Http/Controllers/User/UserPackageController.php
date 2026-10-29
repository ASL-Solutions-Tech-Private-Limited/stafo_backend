<?php

namespace App\Http\Controllers\User;

use App\Models\Package;
use App\Models\Features;
use App\Models\CompanyDetail;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        $packages = Package::with('features')->where('status', 'active')->orderBy('price', 'asc')->get();
        $userId = Auth::id();
        $company = CompanyDetail::with('package')->find($userId);
        $features = Features::where('status', 1)->get();

        return view('user.packages.index', compact('packages', 'company', 'features'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getPackage(Request $request){
        //dd($request->package_id);
        $userId = Auth::id();
        $company = CompanyDetail::find($userId);
        $company->package_id = $request->package_id;
        $company->package_price = $request->price;
        $company->subscription_start = date('Y-m-d');
        $company->subscription_end = date('Y-m-d', strtotime("+".$request->duration." days", strtotime(date("Y-m-d"))) );
        $company->save();

        return redirect()->route('subscriptionDetails')->with('success', 'Subscription successfully.');
    }

    public function subscriptionDetails(){
        $userId = Auth::id();
        $company = CompanyDetail::find($userId);
        $package = Package::find($company->package_id);
        return view('user.packages.subscription-details', compact('company','package'));

    }

    public function success(Request $request){
        $userId = $request->udf1;
        $duration = $request->udf2;
        $packageId = $request->udf3;

        // Verify PayU signature if hash is present
        $salt = env('PAYU_SALT', 'tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq');
        $merchantKey = env('PAYU_MERCHANT_KEY', '1AJhSD');
        $postedHash = strtolower((string)$request->input('hash', ''));

        $isVerified = false;
        if (!empty($postedHash)) {
            $status = $request->input('status', '');
            $txnid = $request->input('txnid', '');
            $amount = $request->input('amount', '');
            $productinfo = $request->input('productinfo', '');
            $firstname = $request->input('firstname', '');
            $email = $request->input('email', '');
            $udf1 = $request->input('udf1', '');
            $udf2 = $request->input('udf2', '');
            $udf3 = $request->input('udf3', '');
            $udf4 = $request->input('udf4', '');
            $udf5 = $request->input('udf5', '');
            $additionalCharges = $request->input('additionalCharges', '');

            if (!empty($additionalCharges)) {
                $hashSeq = $additionalCharges . '|' . $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $merchantKey;
            } else {
                $hashSeq = $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $merchantKey;
            }

            if (hash_equals(strtolower(hash('sha512', $hashSeq)), $postedHash)) {
                $isVerified = true;
            }
        } elseif (Auth::check() && Auth::id() == $userId) {
            // Already authenticated user in active session
            $isVerified = true;
        }

        if (!$isVerified && !app()->environment('local')) {
            return redirect()->route('login')->with('error', 'Payment verification failed. Invalid response signature.');
        }

        $company = CompanyDetail::find($userId);
        if ($company && $packageId) {
            $company->package_id = $packageId;
            $company->package_price = $request->amount;
            $company->subscription_start = date('Y-m-d');
            $company->subscription_end = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
            $company->save();
        }

        PaymentInfo::create([
            'company_id' => $userId,
            'transaction_id' => $request->txnid,
            'payment_status' => $request->status,
            'package_id' => $packageId,
            'payment_amount' => $request->amount,
            'message' => $request->error_Message,
            'payment_info' => json_encode($request->all())
        ]);

        if ($company && !Auth::check() && $isVerified) {
            Auth::login($company);
        }

        $package = Package::find($packageId);
        $subscription = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
        return view('user.packages.success', compact('package','subscription','request'));
    }

    public function failure(Request $request){
        if (!empty($request->udf1)) {
            PaymentInfo::create([
                'company_id' => $request->udf1,
                'transaction_id' => $request->txnid,
                'payment_status' => $request->status ?? 'failure',
                'package_id' => $request->udf3,
                'payment_amount' => $request->amount,
                'message' => $request->error_Message,
                'payment_info' => json_encode($request->all())
            ]);
        }

        // Only render failure view if authenticated, otherwise redirect to login
        if (Auth::check()) {
            return view('user.packages.failure', compact('request'));
        }

        return redirect()->route('login')->with('error', 'Payment failed or was cancelled. Please log in to retry.');
    }

    
}