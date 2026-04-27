<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\CompanyType;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\ProprietorDetail;
use App\Models\Branch;
use App\Models\Chat;
use App\Models\CompanyDocument;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\Ticket;
use App\Models\CompanyReferralCode;
use RealRashid\SweetAlert\Facades\Alert;

class CompanyDetailController extends Controller
{
    public function index(Request $request)
    {
        $companies  = CompanyDetail::orderBy('company_name', 'asc')->get();
        $data = CompanyDetail::query()
            ->when($request->company_name, fn($q) => $q->where('company_name', 'like', '%' . $request->company_name . '%'))
            ->when($request->mobile_no, fn($q) => $q->where('mobile_no', $request->mobile_no))
            ->when($request->from_date, fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.companydetails.list', compact('data','companies'));
    }

    public function create()
    {

        $countries = Country::all();
        $states = State::all();

        // dd($states);
        $cities = City::all();
        $companytypes = CompanyType::where('status', '1')->get();
        $business_types = BusinessType::all();
        return view('admin.companydetails.create', compact('countries', 'states', 'cities', 'companytypes', 'business_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|max:255',
            'mobile_no' => 'required|unique:company_details,mobile_no',
            'email' => 'nullable|unique:company_details,email',
            // 'company_type' => 'required|max:255',
            // 'registration_number' => 'required|max:50|unique:company_details,registration_number',
            // 'gst_number' => 'required|max:15|unique:company_details,gst_number',
            // 'pan_number' => 'required|max:10|unique:company_details,pan_number',
            // 'address' => 'required|max:500',
            // 'city' => 'required|max:255',
            // 'state' => 'required|max:255',
            // 'country' => 'required|max:255',
            // 'pin' => 'required|digits:6',
            // 'bank_name' => 'required|max:255',
            // 'account_number' => 'required|digits_between:9,18|unique:company_details,account_number',
            // 'ifsc_code' => 'required|max:11',
            //'no_of_employee' => 'nullable|integer|min:0',
            // 'status' => 'required|in:1,0',
        ]);

        $companyDetail = CompanyDetail::create([
            //'proprietor_id' => $request->proprietor_id,
            'company_name' => $request->company_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'company_type' => $request->company_type,
            'business_type_id' => $request->business_type_id,
            'registration_number' => $request->registration_number,
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pin' => $request->pin,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'no_of_employee' => (isset($request->no_of_employee) ?: 0),
            'status' => $request->status,
            'max_employee_add' => $this->sitesetting(5),
        ]);

        Alert::success('Success', 'Company details have been saved successfully.');
        return redirect()->route('company.details.list')->with('success', 'Company details created successfully.');
    }

    public function edit($id)
    {
        $company = CompanyDetail::where('id', $id)->first();

        $proprietor = ProprietorDetail::where('company_id', $company->id)->first();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $business_types = BusinessType::all();
        $companytypes = CompanyType::where('status', '1')->get();
        return view('admin.companydetails.edit', compact('company', 'countries', 'states', 'cities', 'companytypes', 'business_types', 'proprietor'));
    }

    // public function show($id)
    // {
    //     $company = CompanyDetail::findOrFail($id);
    //     return view('admin.companydetails.show', compact('company'));
    // }

    public function update(Request $request, $id)
    {

        $request->validate([
            'company_name' => 'required|string|max:255',
            'mobile_no' => 'required|string|regex:/^\+?[0-9]*$/|unique:company_details,mobile_no,' . $id,
            'email' => 'required|email|max:255|unique:company_details,email,' . $id,
        ]);

        $company = CompanyDetail::findOrFail($id);
        $company->update([
            'company_name' => $request->company_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'company_type' => $request->company_type,
            'business_type_id' => $request->business_type_id,
            'registration_number' => $request->registration_number,
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'pin' => $request->pin,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'no_of_employee' => (isset($request->no_of_employee) ?: 0),
            'max_employee_add' => $request->max_employee_add,
            'subscription_start' => $request->subscription_start,
            'subscription_end' => $request->subscription_end,
            'status' => $request->status,
            'map_view' => $request->map_view,
        ]);



        $proprietor = ProprietorDetail::where('company_id', $company->id)->first();


        if (empty($proprietor)) {
            $proprietor = new ProprietorDetail();
            $proprietor->company_id = $company->id;
        }

        $proprietor->first_name = $request->first_name;
        $proprietor->last_name = $request->last_name;
        $proprietor->mobile = $request->mobile;
        $proprietor->email = $request->owner_email;
        $proprietor->current_address = $request->current_address;
        $proprietor->save();

        Alert::success('Success', 'Company details have been updated successfully.');
        return redirect()->route('company.details.list')->with('success', 'Company updated successfully.');
    }

    public function destroy($id)
    {
        $company = CompanyDetail::findOrFail($id);
        $company->delete();
        // Delete the associated proprietor detail if it exists
        $proprietor = ProprietorDetail::where('company_id', $id)->first();
        if ($proprietor) {
            $proprietor->delete();
        }
        // Delete the associated branches if they exist
        $branches = Branch::where('company_id', $id)->get();
        foreach ($branches as $branch) {
            $branch->delete();
        }
        // Delete the associated employees if they exist
        $employees = Employee::where('company_id', $id)->get();
        foreach ($employees as $employee) {
            $employee->delete();
        }
        // Delete the associated shifts if they exist
        $shifts = Shift::where('company_id', $id)->get();
        foreach ($shifts as $shift) {
            $shift->delete();
        }
        // Delete the associated tickets if they exist
        $tickets = Ticket::where('company_id', $id)->get();
        foreach ($tickets as $ticket) {
            $ticket->delete();
        }
        // Delete the associated chats if they exist
        $chats = Chat::where('company_id', $id)->get();
        foreach ($chats as $chat) {
            $chat->delete();
        }
        // Delete the associated company documents if they exist
        $companyDocuments = CompanyDocument::where('company_id', $id)->get();
        foreach ($companyDocuments as $document) {
            $document->delete();
        }
        // Delete the associated departments if they exist
        $departments = Department::where('company_id', $id)->get();
        foreach ($departments as $department) {
            $department->delete();
        }
               

        Alert::success('Success', 'company Detail been deleted successfully.');
        return redirect()->route('company.details.list')->with('success', 'Company Detail deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $company = CompanyDetail::findOrFail($id);


        $company->status = $company->status == 1 ? 0 : 1;
        $company->save();

        return response()->json([
            'success' => true,
            'new_status' => $company->status,
        ]);
    }

    public function show($id)
    {
        $company = CompanyDetail::findOrFail($id);       
        $proprietor = ProprietorDetail::where('company_id', $company->id)->first();

        return view('admin.companydetails.show', compact('company', 'proprietor'));
    }

    public function referralCodeList($id,Request $request){
        $data = CompanyReferralCode::where('company_id', $id)
            ->when($request->referralcode, fn($q) => $q->where('referralcode', 'like', '%' . $request->referralcode . '%'))            
            ->orderBy('id', 'desc')
            ->paginate(10);
        $company_id = $id;
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.referralcode.list', compact('data','id','company_id','companies'));
    }
    public function createReferralCode($id,Request $request){
        $company = CompanyDetail::select('id','company_name')->findOrFail($id);
        return view('admin.referralcode.create', compact('company'));
    }

    public function storeReferralCode(Request $request){
        $request->validate([
            'referralcode' => 'required|max:255|unique:company_referral_codes,referralcode'
        ]);

        CompanyReferralCode::create([
            'company_id' => $request->company_id,
            'referralcode' => $request->referralcode,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 1,
        ]);
        Alert::success('Success', 'Referral code has been saved successfully.');
        return redirect()->route('referralCodeList',$request->company_id)->with('success', 'Record added successfully.');
        
    }

    public function editReferralCode($id,Request $request){
        $referralcode = CompanyReferralCode::findOrFail($id);
        $company = CompanyDetail::select('id','company_name')->findOrFail($referralcode->company_id);
        return view('admin.referralcode.edit', compact('company','referralcode'));
    }

    public function updateReferralCode($id,Request $request){
        
        $referralcode = CompanyReferralCode::findOrFail($id);
        $referralcode->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        Alert::success('Success', 'Referral code has been updated successfully.');
        return redirect()->route('referralCodeList',$request->company_id)->with('success', 'Record updated successfully.');
        
    }

    public function destroyReferralCode($id){
        $referralcode = CompanyReferralCode::findOrFail($id);
        $company_id = $referralcode->company_id;
        $referralcode->delete();
        Alert::success('Success', 'Referral code has been deleted successfully.');
        return redirect()->route('referralCodeList',$company_id)->with('success', 'Record deleted successfully.');
    }

    public function referralcodeStatus($id)
    {
        $CompanyReferralCode = CompanyReferralCode::findOrFail($id);
        $CompanyReferralCode->status = $CompanyReferralCode->status == 1 ? 0 : 1;
        $CompanyReferralCode->save();

        return response()->json([
            'success' => true,
            'new_status' => $CompanyReferralCode->status,
        ]);
    }

    public function referralcode(){
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.referralcode.index',compact('companies'));
    }

    public function pinchecking(Request $request)
    {
        $pin = $request->pin;
        $pinNo = '777444';
        //$company = CompanyDetail::where('pin', $pin)->first();
        if ($pinNo == $pin) {
            return response()->json(['status' => 'success', 'message' => 'Pin is valid.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Pin is invalid.']);
        }
    }
}