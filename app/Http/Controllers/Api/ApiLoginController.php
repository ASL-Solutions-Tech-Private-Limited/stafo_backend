<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Employee;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\DeviceSession;
use App\Models\ProprietorDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Helpers\SmsHelper;
use Illuminate\Support\Str;
use App\Models\CompanyReferralCode;
use App\Models\GraceSetting;


class ApiLoginController extends Controller
{

    public function register(Request $request)
    {

        // Validate input data (making all fields optional)
        $request->validate([
            'company_info.company_name' => 'nullable|string|max:255',
            'company_info.email' => 'required|unique:company_details,email',
            'company_info.mobile_no' => 'required|numeric|unique:company_details,mobile_no',
            'owner_info.mobile' => 'nullable|unique:proprietor_details,mobile',
            'owner_info.email' => 'nullable|unique:proprietor_details,email',
            'referral_code' => 'nullable|string'

        ]);

        try {


            $companyCode = strtoupper(substr($request->company_info['company_name'], 0, 3)) . strtoupper(Str::random(5));
            $referralCode = strtoupper(Str::random(8)); // Generate a unique referral code

            $referralcode_used = null;
            if ($request->filled('referral_code')) {
                $referral = CompanyReferralCode::where('referralcode', $request->referral_code)->first();
                if ($referral) {
                    $today = date('Y-m-d');
                    if($referral->end_date < $today) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Referral code has expired.'
                        ], 200);
                       
                    }
                    else if($referral->start_date > $today) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Referral code is not activated yet.'
                        ], 200);
                        
                    }else if($referral->status == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Referral code is not active.'
                        ], 200);
                        
                    } else{
                        $referralcode_used = $referral->referralcode;
                        $referral->use_count += 1;
                        $referral->save();
                    }
                    
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid referral code.'
                    ], 200);
                    
                }
            }
            $companyData = [
                'company_name' => $request->company_info['company_name'] ?? null,
                'company_type' => $request->company_info['company_type'] ?? null,
                'registration_number' => $request->company_info['registration_number'] ?? null,
                'gst_number' => $request->company_info['gst_number'] ?? null,
                'pan_number' => $request->company_info['pan_number'] ?? null,
                'country' => $request->company_info['country'] ?? null,
                'state' => $request->company_info['state'] ?? null,
                'city' => $request->company_info['city'] ?? null,
                'address' => $request->company_info['address'] ?? null,
                'pin' => $request->company_info['pin'] ?? null,
                'mobile_no' => $request->company_info['mobile_no'] ?? null,
                'email' => $request->company_info['email'] ?? null,
                'referral_code' => $referralCode,
                'referralcode_used' => $referralcode_used,
                'max_employee_add' => $this->sitesetting(5)

            ];
            if (empty($companyData['registration_number'])) {
                unset($companyData['registration_number']);
            }
            $company = CompanyDetail::create($companyData);


            if ($company) {
                $token = $company->createToken('auth_token')->plainTextToken;
            }
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
            // dd($company);
            $proprietorData = [
                'company_id' => $company['id'],
                'first_name' => $request->owner_info['first_name'] ?? null,
                'last_name' => $request->owner_info['last_name'] ?? null,
                'mobile' => $request->owner_info['mobile'] ?? null,
                'email' => $request->owner_info['email'] ?? null,
            ];
            $proprietor = ProprietorDetail::create($proprietorData);

            // Send the welcome email (if applicable)
            //Mail::to($company->email)->send(new WelcomeEmail($company));

            // Return a response with success message and status 201 (Created)
            return response()->json([
                'success' => true,
                'message' => 'Company registered successfully.',
                'token' =>  $token,
                'company' => $companyData,
                'referrer' => $referrer->company_name ?? null
            ], 200);
        } catch (\Exception $e) {
            // If there's an error, return a response with failure message and status 500 (Internal Server Error)
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    public function login(Request $request)
    {
        // dd("test");
        try {
            // Validate input data
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Fetch user from the ProprietorDetail table
            $user = CompanyDetail::where('email', $request->email)->first();

            // dd($user);

            // Check if the user exists and password matches
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials are incorrect.',
                ], 200);
            }

            // Generate a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the token as a response
            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data' => [
                    'token' => $token,
                    'company' => $user,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function sendOtp(Request $request)
    {
        try {
            $input = trim($request->input('mobile_number'));

            if (empty($input)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a mobile number or email.',
                ], 200);
            }

            // Check if input is an email address
            if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $user = CompanyDetail::where('email', $input)->where('status', '1')->first();
                if (!$user) {
                    $user = Employee::where(function($q) use ($input) {
                        $q->where('email', $input)->orWhere('official_email_id', $input);
                    })->where('status', '1')->first();
                }
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No active company or employee found with this email.',
                    ], 200);
                }
                $phone = $user instanceof Employee ? $user->phone : $user->mobile_no;
                if (empty($phone)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No registered mobile number associated with this account.',
                    ], 200);
                }
            } else {
                $phone = preg_replace('/[^0-9]/', '', $input);
                if (strlen($phone) > 10) {
                    $phone = substr($phone, -10);
                }
                if (strlen($phone) < 10) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter a valid 10-digit mobile number.',
                    ], 200);
                }

                $user = CompanyDetail::where('mobile_no', $phone)->where('status', '1')->first();
                if (!$user) {
                    $user = Employee::where('phone', $phone)->where('status', '1')->first();
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No registered company or active employee account found with this number.',
                ], 200);
            }

            // Generate a 6-digit OTP
            $otp = rand(100000, 999999);
            if ($phone == '9999999999' || $phone == '8888888888') {
                $otp = 111111;
            }

            $user->otp = $otp;
            $user->save();

            // Cache OTP for both phone and input
            Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));
            if ($input !== $phone) {
                Cache::put('otp_' . $input, $otp, now()->addMinutes(10));
            }

            // Call the SmsHelper to send the OTP (if not demo)
            if ($phone == '9999999999' || $phone == '8888888888') {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully. (Demo OTP: 111111)',
                    'otp' => $otp,
                    'user_type' => $user instanceof Employee ? 'employee' : 'company',
                    'phone' => substr($phone, 0, 2) . '******' . substr($phone, -2),
                ], 200);
            }

            $response = SmsHelper::sendOtp1($phone, $otp);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to registered number.',
                    'otp' => $otp,
                    'user_type' => $user instanceof Employee ? 'employee' : 'company',
                    'phone' => substr($phone, 0, 2) . '******' . substr($phone, -2),
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP.',
                    'error' => $response['message'] ?? 'SMS gateway error',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //  Verify OTP and login
    // public function loginWithOtp(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'mobile_number' => 'required|digits:10',
    //             'otp' => 'required|digits:6',
    //             'device_id' => 'required|string',

    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid mobile number or OTP.',
    //                 'errors' => $validator->errors(),
    //             ], 422);
    //         }
    //         $cachedOtp = Cache::get('otp_' . $request->mobile_number);
    //         if (!$cachedOtp || $cachedOtp != $request->otp) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid OTP.',
    //             ], 401);
    //         }
    //         // Generate a token for the session (not related to any user table)
    //         //$token = bin2hex(random_bytes(32));
    //         $token = '';
    //         $employee = $company = null;
    //         $user = $company = CompanyDetail::where('mobile_no', $request->mobile_number)->first();
    //         if (!$user) {
    //             $user = $employee = Employee::where('phone', $request->mobile_number)->first();
    //         }
    //         if ($user) {

    //             $token = $user->createToken('auth_token')->plainTextToken;

    //             // Store device_id in the database
    //             $user->update(['device_id' => $request->device_id]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Otp verify successful.',
    //             'data' => [
    //                 'token' => $token,
    //                 'company' => $company,
    //                 'employee' => $employee,
    //                 'device_id' => $request->device_id
    //             ],
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An unexpected error occurred.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }



    public function loginWithOtp(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|digits:10',
                'otp' => 'required|digits:6',
                'device_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid mobile number or OTP.',
                    'errors' => $validator->errors(),
                ], 200);
            }

            // Retrieve cached OTP
            $cachedOtp = Cache::get('otp_' . $request->mobile_number);
            if (!$cachedOtp || $cachedOtp != $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ], 200);
            }


            // Fetch user details based on mobile number (either Employee or Company)
            $user = CompanyDetail::where('mobile_no', $request->mobile_number)->where('status', '1')->first();
            $user_type = 'company';
            if (!$user) {
                $user = Employee::where('phone', $request->mobile_number)->where('status', '1')->first();
                $user_type = 'employee';
            }

            if ($user) {
                $user->fcm_token = $request->firebase_token;
                $user->device_name = $request->device_name;
                $user->android_version = $request->android_version;
                $user->save();
                // Check if device_id is blank for both Employee and Company, and update it
                if (empty($user->device_id)) {
                    // Update the device_id if it's blank
                    $user->device_id = $request->device_id;
                    $user->save();
                } else {
                    if ($request->device_id != $user->device_id) {
                        $device = new DeviceSession;

                        if ($user_type == 'company') {
                            $device->company_device_id = $request->device_id;
                            $device->company_id = $user->id;
                            $token = $user->createToken('auth_token')->plainTextToken;
                        } else {
                            $device->employee_device_id = $request->device_id;
                            $device->employee_id = $user->id;
                            $token = '';
                        }
                        
                        $device->save();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'This employee is logged in anoter device.',
                            'data' => [
                                'token' => $token,
                                'company' => $user instanceof CompanyDetail ? $user : null,  // Only return company details if it's a company user
                                'employee' => $user instanceof Employee ? $user : null,  // Only return employee details if it's an employee user
                                'device_id' => $request->device_id,
                                'device_change' => "yes",
                            ],
                        ], 200);
                    }
                }

                // Generate a token for the user
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully.',
                    'data' => [
                        'token' => $token,
                        'company' => $user instanceof CompanyDetail ? $user : null,  // Only return company details if it's a company user
                        'employee' => $user instanceof Employee ? $user : null,  // Only return employee details if it's an employee user
                        'device_id' => $request->device_id,
                        'fcm_token'=> $request->firebase_toke,
                        'device_name' => $request->device_name,
                        'android_version' => $request->android_version,
                        'device_change' => "no",
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeDevice(Request $request)
    {
        try {
            $status = $request->status;
            $employee_id = $request->employee_id;
            $company_id = $request->company_id;
            if ($employee_id) {
                $user = Employee::find($employee_id);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Employee not found.',
                    ], 200);
                }
                $device = DeviceSession::where('employee_id', $employee_id)->latest()->first();
                
                if (!$device && $request->filled('device_id')) {
                    $device = new DeviceSession;
                    $device->employee_device_id = $request->device_id;
                    $device->employee_id = $user->id;
                    $device->company_id = $user->company_id;
                    $device->save();
                }

                if ($request->filled('device_name')) {
                    $user->device_name = $request->device_name;
                }

                if ($device && $status === 'pending') {
                    $user->device_status = 'pending';
                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Please wait, company will approve your device request.',
                    ], 200);
                }

                if ($device && ($status === 'approve' || $status === 'approved')) {
                    $user->device_id = $device->employee_device_id;
                    $user->device_status = 'approved';
                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Device ID updated successfully.',
                    ], 200);
                }

                // If no device record is found
                return response()->json([
                    'success' => false,
                    'message' => 'No device record found for this employee.',
                ], 200);
            }

            // Handle the company request
            if ($company_id) {
                // Fetch the company from the database
                $user = CompanyDetail::find($company_id);

                // Check if the company exists
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Company not found.',
                    ], 200);
                }

                // Find the last inserted device record for this company
                $device = DeviceSession::where('company_id', $company_id)->latest()->first();
                if (!$device && $request->filled('device_id')) {
                    $device = new DeviceSession;
                    $device->company_device_id = $request->device_id;
                    $device->employee_device_id = $request->device_id;
                    $device->company_id = $user->id;
                    $device->save();
                }

                // If a device record is found and the status is "approved", update the device ID
                if ($device && ($status === 'approve' || $status === 'approved')) {
                    $user->device_id = $device->employee_device_id ?: $device->company_device_id;
                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Device ID updated successfully.',
                    ], 200);
                }

                // If no device record is found
                return response()->json([
                    'success' => false,
                    'message' => 'No device record found for this company.',
                ], 200);
            }

            // If neither employee_id nor company_id is provided
            return response()->json([
                'success' => false,
                'message' => 'Invalid request. Provide either employee_id or company_id.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveDeviceRequest(Request $request)
    {
        try {

            $status = $request->status;
            $employee_id = $request->employee_id;
            $newdevice_id =  $request->device_id;


            if (!in_array($status, ['approve', 'reject'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. It should be either "approve" or "reject".'
                ], 200);
            }

            // Find the employee by ID
            $employee = Employee::find($employee_id);

            // Check if the employee exists
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.'
                ], 200);
            }

            // Check if the employee's device status is pending
            if ($employee->device_status != 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'The device request is not in pending status.'
                ], 200);
            }

            // If status is approve, update the employee's device ID and set device status to 'approved'
            if ($status === 'approve') {
                $device = DeviceSession::where('employee_id', $employee->id)->latest()->first();
                $employee->device_status = 'approve';
                $employee->device_id =  $device->employee_device_id;
                $employee->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Device request approved and device ID updated successfully.'
                ], 200);
            }

            // If status is reject, set the device status to 'rejected'
            if ($status === 'reject') {
                $employee->device_status = 'rejected'; // Update device status to rejected
                $employee->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Device request rejected.'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listDeviceRequests(Request $request)
    {
        try {
            $statusFilter = $request->status;
            $companyId = $request->company_id;
            $query = Employee::whereNotNull('device_status');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            if ($statusFilter) {
                $query->where('device_status', $statusFilter);
            }
            $deviceRequests = $query->get();

            $approvedCount = $deviceRequests->where('device_status', 'approved')->count();
            $rejectedCount = $deviceRequests->where('device_status', 'rejected')->count();
            return response()->json([
                'success' => true,
                'message' => 'Device requests retrieved successfully.',
                'approvedCount' => $approvedCount,
                'rejectedCount' => $rejectedCount,
                'data' => $deviceRequests
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}