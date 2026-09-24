<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\ProprietorDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
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
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }
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
        $input = trim($request->email);
        $mobilePattern = '/^\+?[0-9]{10,14}$/'; // Phone number pattern (including 10-digit Indian numbers)

        // Case 1: Phone number (Mobile + OTP)
        if (preg_match($mobilePattern, $input)) {
            $request->validate([
                'email' => 'required',
                'otp' => 'required'
            ]);
            $otp = trim($request->otp);

            // A. Check if it is a Company user
            $company = CompanyDetail::where('mobile_no', $input)
                ->where(function ($query) use ($otp, $input) {
                    $query->where('otp', $otp);
                    $cachedOtp = Cache::get('otp_' . $input);
                    if ($cachedOtp) {
                        $query->orWhereRaw('? = ?', [$otp, $cachedOtp]);
                    }
                })
                ->first();

            if ($company) {
                Auth::guard('web')->login($company);
                $request->session()->regenerate();

                return redirect()->route('user.dashboard')
                    ->withSuccess('You have successfully logged in!')
                    ->with('user_id', $company->id);
            }

            // B. Check if it is an Employee user
            $employee = Employee::where('phone', $input)
                ->where('status', '1')
                ->where(function ($query) use ($otp, $input) {
                    $query->where('otp', $otp);
                    $cachedOtp = Cache::get('otp_' . $input);
                    if ($cachedOtp) {
                        $query->orWhereRaw('? = ?', [$otp, $cachedOtp]);
                    }
                })
                ->first();

            if ($employee) {
                Auth::guard('employee')->login($employee);
                $request->session()->regenerate();

                return redirect()->route('employee.dashboard')
                    ->withSuccess('Welcome back, ' . ($employee->name ?? 'Employee') . '! You have successfully logged in.')
                    ->with('employee_id', $employee->id);
            }
        } else {
            // Case 2: Email input
            if ($request->filled('otp')) {
                // Email + OTP (for Employee login via registered email)
                $otp = trim($request->otp);

                $employee = Employee::where(function ($q) use ($input) {
                    $q->where('email', $input)->orWhere('official_email_id', $input);
                })
                ->where('status', '1')
                ->where(function ($query) use ($otp, $input) {
                    $query->where('otp', $otp);
                    $cachedOtp = Cache::get('otp_' . $input);
                    if ($cachedOtp) {
                        $query->orWhereRaw('? = ?', [$otp, $cachedOtp]);
                    }
                })
                ->first();

                if ($employee) {
                    Auth::guard('employee')->login($employee);
                    $request->session()->regenerate();

                    return redirect()->route('employee.dashboard')
                        ->withSuccess('Welcome back, ' . ($employee->name ?? 'Employee') . '! You have successfully logged in.')
                        ->with('employee_id', $employee->id);
                }
            }

            // Password login (for Company)
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();
                $userId = Auth::guard('web')->id();

                return redirect()->route('user.dashboard')
                    ->withSuccess('You have successfully logged in!')
                    ->with('user_id', $userId);
            }
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
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }
        if (Auth::guard('web')->check()) {
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
        if (Auth::guard('employee')->check()) {
            Auth::guard('employee')->logout();
        }
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}