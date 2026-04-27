<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ProprietorDetail;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProprietorDetailController extends Controller
{
    public function index(Request $request)
    {

        $data = ProprietorDetail::query()
            ->when($request->first_name, fn($q) => $q->where('first_name', 'like', '%' . $request->first_name . '%'))
            ->when($request->last_name, fn($q) => $q->where('last_name', 'like', '%' . $request->last_name . '%'))
            ->when($request->email, fn($q) => $q->where('email', 'like', '%' . $request->email . '%'))
            ->paginate(10);
        return view('admin.proprietor.index', compact('data'));
    }

    public function create()
    {
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('admin.proprietor.create', compact('countries', 'states', 'cities'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_no' => 'required|digits:10|unique:proprietor_details,mobile_no',
            'email' => 'required|email|unique:proprietor_details,email',
            'aadhar' => 'required|digits:12|unique:proprietor_details,aadhar',
            'pan' => 'required|unique:proprietor_details,pan',
            'current_address' => 'required|max:500',
            //'current_city' => 'required|max:255',
            //'current_state' => 'required|max:255',
            //'current_country' => 'required|max:255',
            'current_pin' => 'required|digits:6',
            'p_address' => 'required|max:500',
            //'p_city' => 'required|max:255',
            //'p_state' => 'required|max:255',
            //'p_country' => 'required|max:255',
            //'p_pin' => 'required|digits:6',
            'status' => 'required|in:1,0',
        ]);





        $proprietorDetail = ProprietorDetail::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'aadhar' => $request->aadhar,
            'pan' => $request->pan,
            'current_address' => $request->current_address,
            'current_city' => $request->current_city,
            'current_state' => $request->current_state,
            'current_country' => $request->current_country,
            'current_pin' => $request->current_pin,
            'p_address' => $request->p_address,
            'p_city' => $request->p_city,
            'p_state' => $request->p_state,
            'p_country' => $request->p_country,
            'p_pin' => $request->p_pin,
            'status' => $request->status,
        ]);

        Alert::success('Success', 'Proprietor details have been saved successfully.');
        return redirect()->route('proprietor.list')->with('success', 'Proprietor details created successfully.');
    }

    public function edit($id)
    {
        $proprietor = ProprietorDetail::where('id', $id)->first();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        return view('admin.proprietor.edit', compact('proprietor', 'countries', 'states', 'cities'));
    }

    public function show($id)
    {
        $proprietor = ProprietorDetail::findOrFail($id);
        return view('admin.proprietor.show', compact('proprietor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_no' => 'required|digits:10|unique:proprietor_details,mobile_no,' . $id,
            'email' => 'required|email|unique:proprietor_details,email,' . $id,
            'aadhar' => 'nullable|digits:12|unique:proprietor_details,aadhar,' . $id,
            'pan' => 'nullable|unique:proprietor_details,pan,' . $id,
            'current_address' => 'required|string|max:500',
            'current_city' => 'required|string|max:255',
            'current_state' => 'required|string|max:255',
            'current_country' => 'required|string|max:255',
            'current_pin' => 'required|digits:6',
            'p_address' => 'nullable|string|max:500',
            'p_city' => 'nullable|string|max:255',
            'p_state' => 'nullable|string|max:255',
            'p_country' => 'nullable|string|max:255',
            'p_pin' => 'nullable|digits:6',
            'status' => 'required|in:1,0',
        ]);

        $proprietor = ProprietorDetail::findOrFail($id);
        $proprietor->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'aadhar' => $request->aadhar,
            'pan' => $request->pan,
            'current_address' => $request->current_address,
            'current_city' => $request->current_city,
            'current_state' => $request->current_state,
            'current_country' => $request->current_country,
            'current_pin' => $request->current_pin,
            'p_address' => $request->p_address,
            'p_city' => $request->p_city,
            'p_state' => $request->p_state,
            'p_country' => $request->p_country,
            'p_pin' => $request->p_pin,
            'status' => $request->status,
        ]);

        Alert::success('Success', 'Proprietor details have been updated successfully.');
        return redirect()->route('proprietor.list')->with('success', 'Proprietor updated successfully.');
    }

    public function destroy($id)
    {
        $proprietorDetail = ProprietorDetail::findOrFail($id);
        $proprietorDetail->delete();
        Alert::success('Success', 'Proprietor Detail been deleted successfully.');
        return redirect()->route('proprietor.list')->with('success', 'Proprietor Detail deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $proprieto = ProprietorDetail::findOrFail($id);

        $proprieto->status = $proprieto->status == 1 ? 0 : 1;
        $proprieto->save();

        return response()->json([
            'success' => true,
            'new_status' => $proprieto->status,
        ]);
    }
}