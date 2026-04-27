@extends('admin.layouts.layout')

@section('title', 'Edit Lead') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 edit-page">
        <h2 class="mb-3 fw-bold">Edit Lead</h2>

        <form action="{{ route('admin.leadUpdate', $lead->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="employee_id ">Employee</label>
                        <select name="employee_id" class="form-control" required>
                            @if(count($employees) > 0)
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id',$lead->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}</option>
                                @endforeach
                            @else
                                <option value="">No Employees Available</option>
                            @endif
                        </select>                        
                        @error('employee_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name',$lead->name) }}"
                                required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name" class="form-control"
                                value="{{ old('company_name',$lead->company_name) }}">
                            @error('company_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" value="{{ old('email',$lead->email) }}"
                                required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone',$lead->phone) }}"
                                required>
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="lead_from">Lead From</label>
                            <input type="text" name="lead_from" class="form-control" value="{{ old('lead_from',$lead->lead_from) }}"
                                required>
                            @error('lead_from')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="notes">Note</label>
                            <textarea name="notes" class="form-control" rows="4" required>{{ old('notes',$lead->notes) }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="next_date">Next Date</label>
                            <input type="date" name="next_date" class="form-control" value="{{ old('next_date',$lead->next_date) }}" required>
                            @error('next_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary ">Update</button>
                    <a href="{{ route('salarytype.index') }}" class="btn btn-danger ">Cancel</a>
                </div>
            </div>







        </form>
    </div>
@endsection