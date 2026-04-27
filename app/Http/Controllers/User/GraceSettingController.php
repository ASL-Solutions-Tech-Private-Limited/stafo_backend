<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GraceSetting;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class GraceSettingController extends Controller
{
    public function index()
    {
        $company_id = Auth::id();
        $settings = GraceSetting::orderBy('id', 'asc')->where('company_id',$company_id)->paginate(10);
        return view('user.grace_settings.index', compact('settings'));
    }


    public function create()
    {
        return view('user.grace_settings.create');
    }


    public function store(Request $request)
    {
        // Validate the input fields
        $request->validate([
            'name' => [
                'required',
                // 'min:6',
                // 'max:50',
                // 'regex:/^[a-zA-Z0-9]+$/',
            ],
            'value' => 'required',
        ]);

        $gracesetting = new GraceSetting();
        $gracesetting->company_id = Auth::id();
        $gracesetting->name = $request->name;
        $gracesetting->label = str_replace(' ', '_', strtolower($request->name));
        $gracesetting->value = $request->value;
        $gracesetting->status = 'Active';
        $gracesetting->save();


        // Create the new site setting
       
        Alert::success('Success', 'GraceSetting has been saved successfully.');

        return redirect()->route('grace_settings.index')->with('success', 'Setting created successfully.');
    }




    public function show(GraceSetting $graceSetting)
    {
        return view('user.grace_settings.show', compact('graceSetting'));
    }

    public function edit($id)
    {
        $graceSetting = GraceSetting::findOrFail($id);
        return view('user.grace_settings.edit', compact('graceSetting'));
    }


    public function update(Request $request, $id)
    {
        $graceSetting = GraceSetting::findOrFail($id);

        // Validate the input fields
        $request->validate([
            'value' => 'required',
        ]);

        

        // Update the site setting
        $graceSetting->update($request->all());

        Alert::success('Success', 'GraceSetting has been updated successfully.');

        return redirect()->route('grace_settings.index')->with('success', 'Setting updated successfully.');
    }



    public function destroy($id)
    {
        $graceSetting = GraceSetting::findOrFail($id);
        $graceSetting->delete();
        Alert::success('Success', 'GraceSetting Detail been deleted successfully.');
        return redirect()->route('grace_settings.index')->with('success', 'Setting deleted successfully.');
    }
}