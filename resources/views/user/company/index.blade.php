@extends('user.layouts.app')

@section('title', 'Company List')

@section('content')
    <div class="card mt-4 p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between user-access">
            <div class="user-welcome">
                <h5>Company Profile List</h5>
            </div>

        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>S.no</th>
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Mobile No</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->company_name }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->mobile_no }}</td>
                        <td>{{ $company->status }}</td>
                        <td>
                            <a href="{{ route('company.profile.edit', $company->id) }}" class="btn btn-warning btn-sm"><i
                                    class="fas fa-edit"></i> Edit</a>
                            {{-- <form action="{{ route('company.profile.destroy', $company->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>
                                    Delete</button>
                            </form> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
