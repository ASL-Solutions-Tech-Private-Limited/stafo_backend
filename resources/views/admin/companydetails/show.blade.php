@extends('admin.layouts.layout')

@section('title', 'Company Details')
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner t-black">
            <div class="mt-3">
                <h3 class="mb-2">Company Details</h3>
                <div class="table-responsive table-same">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            <tr>
                                <th>Proprietor ID</th>
                                <td>{{ $company->proprietor_id }}</td>
                            </tr>
                            <tr>
                                <th>Company Name</th>
                                <td>{{ $company->company_name }}</td>
                            </tr>
                            <tr>
                                <th>Company Type</th>
                                <td>{{ $company->company_type }}</td>
                            </tr>
                            <tr>
                                <th>Registration Number</th>
                                <td>{{ $company->registration_number }}</td>
                            </tr>
                            <tr>
                                <th>GST Number</th>
                                <td>{{ $company->gst_number }}</td>
                            </tr>
                            <tr>
                                <th>PAN Number</th>
                                <td>{{ $company->pan_number }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $company->address }}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>{{ $company->city }}</td>
                            </tr>
                            <tr>
                                <th>State</th>
                                <td>{{ $company->state }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $company->country }}</td>
                            </tr>
                            <tr>
                                <th>Pin</th>
                                <td>{{ $company->pin }}</td>
                            </tr>
                            <tr>
                                <th>Bank Name</th>
                                <td>{{ $company->bank_name }}</td>
                            </tr>
                            <tr>
                                <th>Account Number</th>
                                <td>{{ $company->account_number }}</td>
                            </tr>
                            <tr>
                                <th>IFSC Code</th>
                                <td>{{ $company->ifsc_code }}</td>
                            </tr>
                            <tr>
                                <th>No of Employees</th>
                                <td>{{ $company->no_of_employee }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge {{ $company->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $company->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if(isset($proprietor))
                <div class="mt-3">
                    <h3>Proprietor Details</h3>
                    <div class="table-responsive table-same">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th>First Name</th>
                                    <td>{{ $proprietor->first_name }}</td>
                                </tr>
                                <tr>
                                    <th>Last Name</th>
                                    <td>{{ $proprietor->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile No</th>
                                    <td>{{ $proprietor->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $proprietor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $proprietor->current_address }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <a href="{{ route('company.details.list') }}" class="btn btn-warning">Back to List</a>
            </div>
        </div>
    </div>
@endsection
