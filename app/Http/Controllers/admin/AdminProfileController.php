<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class AdminProfileController extends Controller
{
    /**
     * Show the Admin Profile & Security page.
     */
    public function index()
    {
        $user = Auth::guard('admin')->user() ?: Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update Admin basic profile data (Name, Email, Phone, Image).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('admin')->user() ?: Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
        ]);

        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');

        // Handle Avatar Upload
        if ($request->hasFile('image')) {
            // Delete previous image if exists
            if ($user->image && File::exists(public_path('uploads/' . $user->image))) {
                File::delete(public_path('uploads/' . $user->image));
            }

            $file = $request->file('image');
            $filename = 'admin_avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $user->image = $filename;
        }

        $user->save();

        if (class_exists(Alert::class)) {
            Alert::success('Success', 'Admin profile updated successfully.');
        }

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Change Admin password.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::guard('admin')->user() ?: Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min'       => 'New password must be at least 6 characters long.',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            if (class_exists(Alert::class)) {
                Alert::error('Error', 'The current password provided is incorrect.');
            }
            return redirect()->route('admin.profile')
                ->withErrors(['current_password' => 'The current password provided is incorrect.'])
                ->withInput();
        }

        // Prevent setting the same password
        if (Hash::check($request->new_password, $user->password)) {
            if (class_exists(Alert::class)) {
                Alert::warning('Notice', 'New password cannot be the same as your current password.');
            }
            return redirect()->route('admin.profile')
                ->withErrors(['new_password' => 'New password cannot be the same as your current password.'])
                ->withInput();
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        if (class_exists(Alert::class)) {
            Alert::success('Success', 'Your password has been changed successfully.');
        }

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
    }
}
