<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Validator;
use Illuminate\Support\Facades\Validator;  // Add this import

use App\Models\User;

class LoginController extends Controller
{
    public function login()
    {
        // If the admin is already logged in, redirect to the dashboard
        if (\Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        } else {
            return view('admin.login');
        }
    }



    public function dologin(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Flash the error message and redirect back with validation errors
            return redirect()->route('admin_login')->withErrors($validator);
        }

        // Get input values
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if the user exists in the database
        $checkUserstatus = User::where('email', $email)->first();

        if ($checkUserstatus) {
            // Attempt to authenticate the user
            if (\Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
                // Successfully authenticated
                return redirect()->route('dashboard');
            } else {
                // Invalid username or password
                return redirect()->route('admin_login')->with('error', 'Invalid username or password');
            }
        } else {
            // User not found
            return redirect()->route('admin_login')->with('error', 'You are not an authorized user');
        }
    }


    public function logout()
    {
        // Logout the user from the admin guard
        \Auth::guard('admin')->logout();

        // Redirect to the admin login page
        return redirect()->route('admin_login');
    }
}