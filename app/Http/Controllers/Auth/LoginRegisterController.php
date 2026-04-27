<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;

use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\ProprietorDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\CompanyReferralCode;
use App\Models\GraceSetting;

class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout',
            'dashboard'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {

        return view('auth.register');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    // public function store(Request $request)
    // {

    //     $request->validate([
    //         'name' => 'required|string|max:250',
    //         'email' => 'required|email|max:250|unique:company_details',
    //         'mobile_no' => 'required|numeric|unique:company_details,mobile_no',
    //         'password' => 'required|min:8|confirmed'
    //     ]);

    //     // Generate a unique company code
    //     $companyCode = strtoupper(substr($request->name, 0, 3)) . strtoupper(Str::random(5));


    //     $proprietor =  CompanyDetail::create([
    //         'company_name' => $request->name,
    //         'company_code' => $companyCode,  // Add the generated company code
    //         'email' => $request->email,
    //         'mobile_no' => $request->mobile_no,
    //         'password' => Hash::make($request->password)
    //     ]);

    //     // Auth::login($proprietor);
    //     // Authenticate the user after registration
    //     $credentials = $request->only('email', 'password');
    //     // Redirect to login page after successful registration
    //     return redirect()->route('login')
    //         ->withSuccess('You have successfully registered! Please login to continue.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:company_details',
            'mobile_no' => 'required|numeric|unique:company_details,mobile_no',
            'password' => 'required|min:8|confirmed',
            'referral_code' => 'nullable|string'
        ]);

        // Generate unique company code and referral code
        $companyCode = strtoupper(substr($request->name, 0, 3)) . strtoupper(Str::random(5));
        $referralCode = strtoupper(Str::random(8)); 

        // Find the referring user if referral_code is provided
        $referralcode_used = null;
        if ($request->filled('referral_code')) {
            $referral = CompanyReferralCode::where('referralcode', $request->referral_code)->first();
            if ($referral) {
                $today = date('Y-m-d');
                if($referral->end_date < $today) {
                    return redirect()->back()->withError('Referral code has expired.')->withInput();
                }
                else if($referral->start_date > $today) {
                    return redirect()->back()->withError('Referral code is not activated yet.')->withInput();
                }else if($referral->status == 0) {
                    return redirect()->back()->withError('Referral code is not active.')->withInput();
                } else{
                    $referralcode_used = $referral->referralcode;
                    $referral->use_count += 1;
                    $referral->save();
                }
                
            } else {
                return redirect()->back()->withError('Invalid referral code.')->withInput();
            }
        }
        //dd($referredById);
        $company = CompanyDetail::create([
            'company_name' => $request->name,
            'company_code' => $companyCode,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password),
            'referral_code' => $referralCode,
            //'referred_by' => $referredById,
            'referralcode_used' => $referralcode_used,
            'status' => 1, // Set status to 1 (active) by default
            'max_employee_add' => $this->sitesetting(5), // Set default max employee add
        ]);

        if($company){
            $proprietor = ProprietorDetail::create([
                'company_id' => $company->id,
                'email' => $request->email,
                'mobile' => $request->mobile_no,
                'status' => 1, // Set status to 1 (active) by default
            ]);

            CompanyReferralCode::create([
                'company_id' => $company->id,
                'referralcode' => $referralCode,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+10 year')),
                'status' => 1, // Set status to 1 (active) by default
            ]);

            GraceSetting::create([
                'company_id' => $company->id,
                'name' => 'Grace Time',
                'label' => 'grace_time',
                'value' => '15',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
            GraceSetting::create([
                'company_id' => $company->id,
                'name' => 'Grace Days',
                'label' => 'grace_days',
                'value' => '3',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
            GraceSetting::create([
                'company_id' => $company->id,
                'name' => 'Over Time Charges',
                'label' => 'over_time_charges',
                'value' => '3',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
        }


        return redirect()->route('login')
            ->withSuccess('You have successfully registered! Please login to continue.');
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */




    public function authenticate(Request $request)
    {
       
        $email = $request->email;
        $mobilePattern = '/^\+?[1-9]\d{1,14}$/'; // Basic mobile number regex for international format
        if (preg_match($mobilePattern, $email)) {
            $credentials = $request->validate([
                'email' => 'required',
                'otp' => 'required'
            ]);
            $otp = $request->otp;
            $user = CompanyDetail::where('mobile_no', $email)->where('otp', $otp)->first();
            if ($user) {
                // Log in the user
                Auth::login($user);
            }
        } else {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
            Auth::attempt($credentials);
        }


        if (Auth::id()) {
            $request->session()->regenerate();

            // Get the authenticated user's ID
            $userId = Auth::id(); // This will get the ID of the logged-in user

            // dd($userId);

            // Optionally, you can pass the user ID to the session or redirect
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!')
                ->with('user_id', $userId); // Send the user ID with the redirect response
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');
    }

    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }

        return redirect()->route('login')
            ->withErrors([
                'email' => 'Please login to access the dashboard.',
            ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }
}