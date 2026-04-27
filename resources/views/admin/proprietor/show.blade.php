@extends('admin.layouts.layout')
@section('title', 'Proprietors Show')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="container mt-4">
                <h2>Proprietor Details</h2>
                <table class="table table-bordered">
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
                            <th>Mobile</th>
                            <td>{{ $proprietor->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $proprietor->email }}</td>
                        </tr>
                        <tr>
                            <th>Aadhar</th>
                            <td>{{ $proprietor->aadhar }}</td>
                        </tr>
                        <tr>
                            <th>PAN</th>
                            <td>{{ $proprietor->pan }}</td>
                        </tr>
                        <tr>
                            <th>Current Address</th>
                            <td>{{ $proprietor->current_address }}</td>
                        </tr>
                        <tr>
                            <th>Current City</th>
                            <td>{{ $proprietor->current_city }}</td>
                        </tr>
                        <tr>
                            <th>Current State</th>
                            <td>{{ $proprietor->current_state }}</td>
                        </tr>
                        <tr>
                            <th>Current Country</th>
                            <td>{{ $proprietor->current_country }}</td>
                        </tr>
                        <tr>
                            <th>Current Pin</th>
                            <td>{{ $proprietor->current_pin }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Address</th>
                            <td>{{ $proprietor->p_address }}</td>
                        </tr>
                        <tr>
                            <th>Permanent City</th>
                            <td>{{ $proprietor->p_city }}</td>
                        </tr>
                        <tr>
                            <th>Permanent State</th>
                            <td>{{ $proprietor->p_state }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Country</th>
                            <td>{{ $proprietor->p_country }}</td>
                        </tr>
                        <tr>
                            <th>Permanent Pin</th>
                            <td>{{ $proprietor->p_pin }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $proprietor->status == 1 ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="{{ route('proprietor.list') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
