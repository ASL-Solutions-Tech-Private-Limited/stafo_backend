<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Feedback;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use Illuminate\Validation\Rule;
use App\Models\ProprietorDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CompanyController extends Controller
{


    public function updateCompany(Request $request)
    {
        $id = Auth::id();

        // dd($id);

        $validator = Validator::make($request->all(), [
            // 'company_name' => 'nullable|string|max:255',
            // 'company_type' => 'nullable|string|max:100',
            // 'business_type_id' => 'nullable|exists:business_types,id',
            // 'registration_number' => 'nullable|string|max:100|unique:company_details,registration_number,' . $id,
            // 'gst_number' => 'nullable|string|max:50|unique:company_details,gst_number,' . $id,
            // 'pan_number' => 'nullable|string|max:20|unique:company_details,pan_number,' . $id,
            // 'address' => 'nullable|string|max:255',
            // 'city_id' => 'nullable|exists:cities,id',
            // 'state_id' => 'nullable|exists:states,id',
            // 'country_id' => 'nullable|exists:countries,id',
            // 'pin' => 'nullable|string|max:10',
            // 'bank_name' => 'nullable|string|max:150',
            // 'account_number' => 'nullable|string|max:50|unique:company_details,account_number,' . $id,
            // 'ifsc_code' => 'nullable|string|max:20',
            // 'no_of_employee' => 'nullable|integer',
            // 'status' => 'nullable|string|max:255',
            //'email' => ['nullable', 'email', Rule::unique('company_details', 'email')->ignore($id)],
            'mobile_no' => ['nullable', 'numeric', Rule::unique('company_details', 'mobile_no')->ignore($id)],
            // 'owner_info.first_name' => 'nullable|string|max:255',
            // 'owner_info.last_name' => 'nullable|string|max:255',
            // 'owner_info.mobile' => 'nullable|string|max:20',
            // 'owner_info.email' => 'nullable|email',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            $company = CompanyDetail::findOrFail($id);
            // Handle Image Upload
            if ($request->hasFile('image_name')) {
                // Construct the full path to the existing image
                $oldImagePath = public_path('uploads/company_logo/' . $company->image_name);

                // Delete the old logo if it exists
                if ($company->image_name && file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }

                $image = $request->file('image_name');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/company_logo'), $imageName);
                $imagePath = $imageName;
            } else {
                $imagePath = $company->image_name;
            }
            // Prepare data to update
            $updateData = [
                'company_name' => $request->company_name ?? $company->company_name,
                'company_type' => $request->company_type ?? $company->company_type,
                'business_type_id' => $request->business_type_id ?? $company->business_type_id,
                'registration_number' => $request->registration_number ?? $company->registration_number,
                'gst_number' => $request->gst_number ?? $company->gst_number,
                'pan_number' => $request->pan_number ?? $company->pan_number,
                'address' => $request->address ?? $company->address,
                'city' => $request->city_id ?? $company->city_id,
                'state' => $request->state_id ?? $company->state_id,
                'country' => $request->country_id ?? $company->country_id,
                'pin' => $request->pin ?? $company->pin,
                'bank_name' => $request->bank_name ?? $company->bank_name,
                'account_number' => $request->account_number ?? $company->account_number,
                'ifsc_code' => $request->ifsc_code ?? $company->ifsc_code,
                'no_of_employee' => $request->no_of_employee ?? $company->no_of_employee,
                'status' => $request->status ?? $company->status,
                'email' => $request->email ?? $company->email,
                'mobile_no' => $request->mobile_no ?? $company->mobile_no,
                'image_name' => $imagePath,
            ];

            // Update company details
            $company->update($updateData);
            $proprietor = ProprietorDetail::where('company_id', $company->id)->first();

            // dd($proprietor);
            $proprietorData = [
                'first_name' => $request->owner_info['first_name'] ?? null,
                'last_name' => $request->owner_info['last_name'] ?? null,
                'mobile' => $request->owner_info['mobile'] ?? null,
                'email' => $request->owner_info['email'] ?? null,
                'aadhar' => $request->owner_info['aadhar'] ?? null,
                'pan' => $request->owner_info['pan'] ?? null,
                'current_city' => $request->owner_info['current_city'] ?? null,
                'current_state' => $request->owner_info['current_state'] ?? null,
                'current_country' => $request->owner_info['current_country'] ?? null,
                'current_address' => $request->owner_info['current_address'] ?? null,
                'current_pin' => $request->owner_info['current_pin'] ?? null,
            ];
            $proprietor->update($proprietorData);

            // Fetch related country, state, and city names based on their IDs only if they exist
            $countryName = $request->country_id ? Country::find($request->country_id)->name : $company->country->name ?? null;

            // dd($countryName);

            $stateName = $request->state_id ? State::find($request->state_id)->name : $company->state->name ?? null;
            $cityName = $request->city_id ? City::find($request->city_id)->name : $company->city->name ?? null;

            return response()->json([
                'status' => true,
                'message' => 'Company details updated successfully.',
                'data' => [
                    'company' => $company,
                    'country_name' => $countryName,
                    'state_name' => $stateName,
                    'city_name' => $cityName,
                    'proprietor' => $proprietor,
                ]
            ], 200);
        } catch (\Exception $e) {
            // If an error occurs, return a failure message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the company.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function showCompanyProfile()
    {
        try {
            // Get the authenticated user's company ID
            $companyId = Auth::id();
            $company = CompanyDetail::findOrFail($companyId);

            // Fetch the proprietor details associated with the company
            $proprietor = ProprietorDetail::where('company_id', $company->id)->first();

            $countryName = $company->country ? Country::find($company->country)->name : null;
            $stateName = $company->state ? State::find($company->state)->name : null;
            $cityName = $company->city ? City::find($company->city)->name : null;
            return response()->json([
                'status' => true,
                'message' => 'Company profile fetched successfully',
                'data' => [
                    'company' => $company,
                    'company_logo' => $company->image_name ? asset('uploads/company_logo/' . $company->image_name) : null, //
                    'proprietor' => $proprietor,
                    'country_name' => $countryName,
                    'state_name' => $stateName,
                    'city_name' => $cityName,
                ]
            ], 200);
        } catch (\Exception $e) {
            // If an error occurs, return a failure message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the company profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected $verification_url = 'https://api.quickekyc.com';
    //protected $verification_key = '9c37d312-939d-48d5-bf13-35e8d01c94e7';
    protected $verification_key = '3cb2ecf4-bf45-452f-a1c7-74037349188f';

    //protected $verification_url = 'https://sandbox.quickekyc.com';
    //protected $verification_key = '8511b981-c361-4adf-bdbe-55e90d3ab996';

    public function documentVerify(Request $request)
    {


        if ($request->type == 'pan') {
            $url = $this->verification_url . '/api/v1/pan/pan';
        }
        $data = [
            'key' => $this->verification_key,
            'id_number' => $request->number,
        ];
        $field_name = '';
        $data_field = '';
        $response_field = '';
        switch ($request->type) {
            case 'pan':
                $url = $this->verification_url . '/api/v1/pan/pan';
                $field_name = 'pan_verify';
                $data_field = 'pan';
                $response_field = 'pan_response';
                break;
            case 'aadhar':
                $url = $this->verification_url . '/api/v1/aadhaar-v2/generate-otp';
                break;
            case 'aadhar-otp':
                $url = $this->verification_url . '/api/v1/aadhaar-v2/submit-otp';
                $data['request_id'] = $request->request_id;
                $data['otp'] = $request->otp;
                $field_name = 'aadhar_verify';
                $data_field = 'aadhar';
                $response_field = 'aadhar_response';
                break;
            case 'voter':
                $url = $this->verification_url . '/api/v1/voter-id/voter-id';
                $field_name = 'voter_verify';
                $data_field = 'voter';
                $response_field = 'voter_response';
                break;
            case 'registration_number':
                $url = $this->verification_url . '/api/v1/corporate/company-details';
                $field_name = 'registration_verify';
                $data_field = 'registration_number';
                $response_field = 'reg_response';
                break;
            case 'gstin':
                $url = $this->verification_url . '/api/v1/corporate/gstin';
                $data['filing_status_get'] = true;
                $field_name = 'gstn_verify';
                $data_field = 'gst_number';
                $response_field = 'gst_response';
                break;
            case 'driving-license':
                $url = $this->verification_url . '/api/v1/driving-license/driving-license';
                $data['dob'] = $request->dob;
                $field_name = 'dl_verify';
                $data_field = 'driving_license';
                $response_field = 'dl_response';
                break;
            default:
                $url = $this->verification_url;
                $field_name = '';
                $response_field = '';
        }
        if ($data_field == 'aadhar') {
            unset($data['id_number']);
        }
        $result = $this->makeCurlRequest($url, $data);
        $data = json_decode($result);
        if ($data->status == 'success') {
            $document_number = $request->number;
            if ($request->employee_id != null) {
                $datainfo = Employee::find($request->employee_id);
            }
            if ($request->company_id != null) {
                $datainfo = CompanyDetail::find($request->company_id);
                if ($data_field == 'pan') {
                    $data_field = 'pan_number';
                }
            }

            if ($field_name != '') {
                $datainfo->{$field_name} = 'Yes';
                $datainfo->{$data_field} = $document_number;
                //if ($data_field == 'aadhar') {
                    $datainfo->is_verified = 'Yes';
                //}
                $datainfo->{$response_field} = $result;
                $datainfo->save();
            }
        }

        return $result;
    }

    // Generate QR code for company info
    public function generateQrCode(Request $request)
    {
        $data = array();
        $data['company_id'] = $request->company_id;
        $data['branch_id'] = $request->branch_id;
        $data['department_id'] = $request->department_id;



        // Convert company data to a JSON string (or any other format you prefer)
        $companyDataString = json_encode($data);
        $companyDataString = base64_encode($companyDataString);
        // Generate the QR code for the company data
        $qrCode = QrCode::size(470)->generate($companyDataString);

        // to Save
        //QrCode::size(300)->format('png')->generate($companyDataString, public_path('uploads/qrcode/qr_code.png'));

        // Return the QR code as an image
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }

    public function feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            $feedback = new Feedback();
            $feedback->admin_id = '1';
            $feedback->company_id = $request->company_id;
            $feedback->employee_id = $request->employee_id;
            $feedback->message = $request->message;
            $message_by = '';
            if ($request->company_id) {
                $message_by = 'Company';
            }
            if ($request->employee_id) {
                $message_by = 'Employee';
            }
            $feedback->message_by = $message_by;
            $feedback->save();

            return response()->json([
                'status' => true,
                'message' => 'Feedback submitted successfully.',
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            // If an error occurs, return a failure message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while submitting feedback.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function feedbackList(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'company_id' => 'required',
        // ]);

        // // Check if validation fails
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors(),
        //     ], 200);
        // }

        try {
            $company_id = $request->company_id;
            $employee_id = $request->employee_id;
            $feedback = Feedback::when($company_id, function ($query, $company_id) {
                return $query->where('company_id', $company_id);
            })->when($employee_id, function ($query, $employee_id) {
                return $query->where('employee_id', $employee_id);
            })->get();

            return response()->json([
                'status' => true,
                'message' => 'Feedback list fetched successfully.',
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            // If an error occurs, return a failure message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching feedback list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCompany()
    {
        $id = Auth::id();
        try {
            $company = CompanyDetail::findOrFail($id);
            if ($company->delete()) {
                Branch::where('company_id', $id)->delete();
                Department::where('company_id', $id)->delete();
                Employee::where('company_id', $id)->delete();
                ProprietorDetail::where('company_id', $id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Company deleted successfully.',
                ], 200);
            }
        } catch (\Exception $e) {
            // If an error occurs, return a failure message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the company.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function upgradeInterested()
    {
        $company = CompanyDetail::find(Auth::id());
        if ($company->upgrade_interested >= 1) {
            $company->upgrade_interested += 1;
        } else {
            $company->upgrade_interested = 1;
        }
        $company->save();
        return response()->json([
            'status' => true,
            'message' => 'Thank you for showing interest in upgrading your account. Our team will get in touch with you soon.',
        ], 200);
    }
}