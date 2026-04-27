<?php

namespace App\Http\Controllers\admin;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Shift;
use App\Models\Department;
use App\Models\JobRole;
use App\Models\EmployeeType;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;


class EmployeeController extends Controller
{


    public function index(Request $request)
    {

        $employees = Employee::query()
            ->when($request->employee_name, fn($q) => $q->where('name', 'like', '%' . $request->employee_name . '%'))
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->phone, fn($q) => $q->where('phone', $request->phone))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));
        $companies = CompanyDetail::orderBy('created_at', 'desc')->get();
        return view('admin.employee.index', compact('employees', 'companies'));
    }


    public function create()
    {
        $countries = Country::all();
        $states = State::all();
        $cities = City::all();
        $departments = Department::all();
        $branch = Branch::all();
        $companies = CompanyDetail::all();
        $shift = Shift::all();
        $employee_types = EmployeeType::all();
        $job_types = JobRole::all();

        // Pass this data to the view
        return view('admin.employee.create', compact('countries', 'states', 'cities', 'departments', 'companies', 'shift', 'employee_types', 'branch', 'job_types'));
    }






    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the incoming data
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'name' => 'required|string|min:6|max:55',
            'email' => 'nullable|email|unique:employees,email',
            'phone' => 'required|digits:10',
            'pin' => 'nullable|digits:6',
            'privileged_leave' => 'nullable|numeric',
            'sick_leave' => 'nullable|numeric',
            'casual_leave' => 'nullable|numeric',
        ]);
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'employee_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/employees'), $imageName);
            $imageNameToStore = $imageName;
        } else {
            $imageNameToStore = null;
        }
        Employee::create([
            'company_id' => $request->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'marital_status' => $request->marital_status,
            'position' => $request->position,
            'salary' => $request->salary,
            'branch_id' => $request->branch_id ?? null,
            'department_id' => $request->department_id ?? null,
            'shift_id' => $request->shift_id ?? null,
            'job_title_id' => $request->job_title_id ?? null,
            'employee_type_id' => $request->employee_type_id ?? null,
            'date_of_joining' => $request->date_of_joining,
            'country' => $request->country_id ?? null,
            'state' => $request->state_id ?? null,
            'city' => $request->city_id ?? null,
            'pin' => $request->pin ?? null,
            'address' => $request->address ?? null,
            'official_email_id' => $request->official_email_id ?? null,
            'esi_number' => $request->esi_number ?? null,
            'pf_number' => $request->pf_number ?? null,
            'privileged_leave' => $request->privileged_leave ?? null,
            'sick_leave' => $request->sick_leave ?? null,
            'casual_leave' => $request->casual_leave ?? null,
            'image' => $imageNameToStore,
        ]);

        $company = CompanyDetail::findOrFail($company_id); 
        $company->increment('employee_added', 1);
        // Show a success alert to the user
        Alert::success('Success', 'Employee Details have been saved successfully.');

        // Redirect to the employee list page with a success message
        return redirect()->route('employees.list')->with('success', 'Employee added successfully.');
    }



    // Display the specified employee
    public function show(Employee $employee)
    {
        $country = Country::find($employee->country);
        // dd($country);
        $state = State::find($employee->state);
        $city = City::find($employee->city);
        return view('admin.employee.show', compact('employee', 'country', 'state', 'city'));
    }




    public function edit(Employee $employee)
    {
        $countries = Country::all();
        $states = State::where('country_id', $employee->country)->get();  // Get states based on employee's
        // dd($states);
        $cities = City::where('state_id', $employee->state)->get(); // Get cities based on employee's state
        $departments = Department::all();
        $branches = Branch::all();
        $shift = Shift::all();
        $employee_types = EmployeeType::all();
        $job_types = JobRole::all();

        // dd($employee);
        // Pass the employee data along with the required data to the view
        return view('admin.employee.edit', compact(
            'employee',
            'countries',
            'states',
            'cities',
            'departments',
            'branches',
            'shift',
            'employee_types',
            'job_types'
        ));
    }







    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|min:6|max:55',
            'email' => 'nullable|email|unique:employees,email,' . $id,
            'phone' => 'required|digits:10',
            'pin' => 'nullable|digits:6',
            'privileged_leave' => 'nullable|numeric',
            'sick_leave' => 'nullable|numeric',
            'casual_leave' => 'nullable|numeric',
        ]);

        // Find the employee and update their details
        $employee = Employee::findOrFail($id);

        // Prepare the data to be updated
        $data = $request->only([
            'name',
            'email',
            'phone',
            'position',
            'salary',
            'branch_id',
            'department_id',
            'shift_id',
            'job_title_id',
            'employee_type_id',
            'date_of_birth',
            'gender',
            'marital_status',
            'blood_group',
            'address',
            'date_of_joining',
            'country', // Added country_id
            'state', // Added state
            'city', // Added city
            'pin',
            'official_email_id',
            'esi_number',
            'pf_number',
            'privileged_leave',
            'sick_leave',
            'casual_leave',
        ]);

        // Handle image upload if there is a file uploaded
        if ($request->hasFile('image')) {
            // Get the file extension
            $extension = $request->file('image')->getClientOriginalExtension();

            // Create a unique filename using the employee id and timestamp
            $imageName = 'employee_' . $id . '_' . time() . '.' . $extension;

            // Move the image to the 'public/images/employees' folder
            $request->file('image')->move(public_path('uploads/employees'), $imageName);

            // Add the image name to the data array (not the full path)
            $data['image'] = $imageName;
        }

        // Update the employee data with the validated data
        $employee->update($data);

        // Redirect back with a success message
        Alert::success('Success', 'Employee details updated successfully.');
        return redirect()->route('employees.list');
    }




    // Remove the specified employee from the database
    public function destroy(Employee $employee)
    {
        $employee->delete();
        Alert::success('Success', 'Employee Details has been Deleted successfully.');

        return redirect()->route('employees.list')->with('success', 'Employee deleted successfully.');
    }
}