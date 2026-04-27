<?php

namespace App\Http\Controllers\User;

use App\Models\City;
use App\Models\State;
use App\Models\Ticket;
use App\Models\Country;
use App\Models\Feedback;
use App\Models\CompanyType;
use App\Models\HelpContent;
use App\Models\TicketReply;
use App\Models\BusinessType;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\ProprietorDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserCompanyDetailController extends Controller
{


    public function feedback()
    {
        // Get the company ID of the authenticated user
        $companyId = Auth::id();

        // Fetch the feedback records in descending order by created_at
        $records = Feedback::with(['company', 'employee'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc') // Order by created_at in descending order
            ->paginate(10);



        return view('user.feedback.list', compact('records'));
    }

    public function createFeedback(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        Feedback::create([
            'company_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Feedback submitted successfully!');
    }

    public function tickets()
    {
        // Get the company ID of the authenticated user
        $companyId = Auth::id();

        // Fetch the feedback records in descending order by created_at
        $records = Ticket::with(['company', 'employee', 'replies'])
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc') // Order by created_at in descending order
            ->get();

        // dd($records);
        $ticket_id = null;
        return view('user.tickets.list', compact('records'));
    }


    public function createTickets(Request $request, $ticket_id = null)
    {
        // dd($ticket_id);
        //dd($request->all());
        // Validate the incoming request
        $request->validate([
            'title' => 'nullable|string|max:255',  // Validate title
            'message' => 'required|string',
        ]);


        // If no ticket_id is provided, create a new ticket
        if ($ticket_id === null) {
            // Create a new ticket
            $ticket = Ticket::create([
                'title' => $request->title,  // Save the title
                'message' => $request->message,
                'message_by' => 'company',
                'company_id' =>  Auth::id(),
                // 'employee_id' => auth()->user()->employee_id,
            ]);
        } else {
            // Otherwise, find the ticket and store the reply
            $ticket = Ticket::findOrFail($ticket_id);

            $ticket->replies()->create([
                'message' => $request->message,
                'message_by' => 'company',
                'company_id' => Auth::id(),
                'employee_id' => auth()->user()->employee_id,
            ]);
        }

        return redirect()->route('company.tickets')->with('success', 'Ticket reply created successfully.');
    }

    public function showReplies(Ticket $ticket)
    {
        // Load replies for the ticket
        $ticket->load('replies'); // eager load the replies

        return view('user.tickets.show_replies', compact('ticket'));
    }


    public function helpList()
    {
        $helpContents = HelpContent::paginate(10);

        //dd($helpContents);

        return view('user.help.index', compact('helpContents'));
    }

    public function index()
    {
        $companies = CompanyDetail::where('id', Auth::id())->get(); // Assuming each company is associated with a

        return view('user.company.index', compact('companies'));
    }




    //
    public function edit()
    {
        $company = CompanyDetail::where('id', Auth::id())->first();
        $proprietor = ProprietorDetail::where('company_id', Auth::id())->first();
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $company_type = CompanyType::all();
        $business_type = BusinessType::all();
        // dd($company);
        return view('user.company.edit', compact('company', 'proprietor', 'countries', 'cities', 'states', 'company_type', 'business_type'));
    }



    public function update(Request $request)
    {
        if ($request->hasFile('companylogo') && $request->file('companylogo')->isValid()) {
            $request->validate([
                'companylogo' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
            ]);
            $file = $request->file('companylogo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/compnay_logo'), $filename);
            $companyLogoPath = $filename;
        } else {
            // If no logo file is uploaded, keep the existing company logo (if any)
            $companyLogoPath = $request->input('existing_company_logo', null);
        }

        // Add validation for email and phone number along with other fields
        $request->validate([
            'mobile_no' => 'required|string|max:15|regex:/^\+?[0-9]*$/',
            'email' => 'required|email|max:255|unique:company_details,email,' . Auth::id(),
        ]);

        // Fetch the company details
        $company = CompanyDetail::where('id', Auth::id())->first();

        // Prepare the data for update, including company logo path if it exists
        $dataToUpdate = $request->all();

        $dataToUpdate['company_name'] = $request->company_name;
        $dataToUpdate['email'] = $request->email;
        //$dataToUpdate['mobile_no'] = $request->mobile_no;
        $dataToUpdate['no_of_employee'] = $request->no_of_employee;
        $dataToUpdate['bank_name'] = $request->bank_name;
        $dataToUpdate['account_number'] = $request->account_number;
        $dataToUpdate['ifsc_code'] = $request->ifsc_code;
        $dataToUpdate['country'] = $request->country;
        $dataToUpdate['state'] = $request->state;
        $dataToUpdate['city'] = $request->city;
        $dataToUpdate['pin'] = $request->pin;
        $dataToUpdate['address'] = $request->address;
        $dataToUpdate['company_type'] = $request->company_types;
        $dataToUpdate['business_type_id'] = $request->business_name;
        // If a new company logo was uploaded, add the path to the data
        if ($companyLogoPath) {
            $dataToUpdate['image_name'] = $companyLogoPath;
        }
        $company->update($dataToUpdate);
        $proprietor = ProprietorDetail::where('company_id', Auth::id())->first();
        if (empty($proprietor)) {
            $proprietor = new ProprietorDetail();
            $proprietor->company_id = Auth::id();
        }

        $proprietor->first_name = $request->first_name;
        $proprietor->last_name = $request->last_name;
        $proprietor->mobile = $request->mobile;
        $proprietor->email = $request->owner_email;
        $proprietor->current_address = $request->current_address;
        $proprietor->save();

        // Redirect back with success message
        Alert::success('Success', 'Company profile updated successfully.');
        return redirect()->route('company.profile.edit')->with('success', 'Company profile updated successfully.');
    }



    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get();
        return response()->json($states);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get();
        return response()->json($cities);
    }

    public function upgradeInterested()
    {
        $company = CompanyDetail::where('id', Auth::id())->first();
        $company->upgrade_interested += 1;
        $company->save();
        Alert::success('Success', 'Thank you for showing interest in upgrading your account. Our team will get in touch with you soon.');
        return redirect()->route('employee.index');
    }

    public function referralList(){
        $company_id =  Auth::id();
        $company = CompanyDetail::findOrFail($company_id);  
        $rcount = $company->referrals->count();
        $rlist = $company->referrals()->select('id','company_name','company_code','email','mobile_no')->get();

        return view('user.company.referral', compact('company','rcount','rlist'));
    }

    public function deviceList(Request $request){
        $company_id =  Auth::id();
        $statusFilter = $request->status;
        $query = Employee::whereNotNull('device_status')->where('company_id',$company_id);
        
        if ($statusFilter) {
            $query->where('device_status', $statusFilter);
        }
        $deviceRequests = $query->get();
        
        return view('user.company.devicelist', compact('deviceRequests'));
    }

    public function approveDevice(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->device_status = 'approve';
        $employee->save();

        Alert::success('Success', 'Device request approved successfully.');
        return redirect()->route('deviceList');

    }

    public function rejectDevice(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->device_status = 'rejected';
        $employee->save();

        Alert::success('Success', 'Device request rejected successfully.');
        return redirect()->route('deviceList');

    }
}