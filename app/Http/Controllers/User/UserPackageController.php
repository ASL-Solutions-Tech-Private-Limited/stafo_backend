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
            'message' => $request->error_Message,
            'payment_info' => json_encode($request->all())
        ]);
        $user = CompanyDetail::find($userId);
        Auth::login($user);

        $package = Package::find($packageId);
        $subscription = date('Y-m-d', strtotime("+".$duration." days", strtotime(date("Y-m-d"))) );
        return view('user.packages.success', compact('package','subscription'));
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

        $user = CompanyDetail::find($request->udf1);
        Auth::login($user);
        
        return view('user.packages.failure', compact('request'));
    }

    
}