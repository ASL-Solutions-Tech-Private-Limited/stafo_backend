<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;


class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.site_settings.index', compact('settings'));
    }


    public function create()
    {
        return view('admin.site_settings.create');
    }


    public function store(Request $request)
    {
        // Validate the input fields
        $request->validate([
            'key' => [
                'required',
                // 'min:6',
                // 'max:50',
                // 'regex:/^[a-zA-Z0-9]+$/',
            ],
            'value' => 'required',
        ]);

        // Check if the key already exists in the database
        if (SiteSetting::where('key', $request->key)->exists()) {
            // Redirect back with an error message if the key exists
            return back()->withErrors(['key' => 'The key already exists. Please choose a different key.'])
                ->withInput();
        }

        // Create the new site setting
        SiteSetting::create($request->all());
        Alert::success('Success', 'SiteSetting has been saved successfully.');

        return redirect()->route('site_settings.index')->with('success', 'Setting created successfully.');
    }




    public function show(SiteSetting $siteSetting)
    {
        return view('admin.site_settings.show', compact('siteSetting'));
    }

    public function edit($id)
    {
        $siteSetting = SiteSetting::findOrFail($id);
        return view('admin.site_settings.edit', compact('siteSetting'));
    }


    public function update(Request $request, $id)
    {
        $siteSetting = SiteSetting::findOrFail($id);

        // Validate the input fields
        $request->validate([
            'value' => 'required',
        ]);

        // Check if the key already exists (additional check for clarity)
        if (SiteSetting::where('key', $request->key)->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['key' => 'The key already exists. Please choose a different key.'])
                ->withInput();
        }

        // Update the site setting
        $siteSetting->update($request->all());

        Alert::success('Success', 'SiteSetting has been updated successfully.');

        return redirect()->route('site_settings.index')->with('success', 'Setting updated successfully.');
    }



    public function destroy($id)
    {
        $siteSetting = SiteSetting::findOrFail($id);
        $siteSetting->delete();
        Alert::success('Success', 'SiteSetting Detail been deleted successfully.');
        return redirect()->route('site_settings.index')->with('success', 'Setting deleted successfully.');
    }
}