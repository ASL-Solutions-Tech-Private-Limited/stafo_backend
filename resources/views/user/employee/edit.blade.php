@extends('user.layouts.app')
@section('title', 'Employee edit')

@section('css')
    <style>

    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="section-header">
                <h2 class="fw-bold">Update Employee</h2>
                <a href="{{ route('employee.index') }}" class="btn btn-success"><i class="fas fa-list"></i> Employee List</a>
            </div>

            <form class="mt-4" action="{{ route('user.employees.update', $employee->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Upload Image -->
                <div class="file-upload-group">
                    <label class="form-label">Profile Picture</label>
                    <input class="form-control" type="file" name="image" accept="image/*">
                    @if ($employee->image)
                        <img src="{{ asset('uploads/employees/' . $employee->image) }}" alt="Profile Image"
                            class="img-thumbnail profile-img mt-2">
                    @endif
                    @error('image')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Upload Resume -->
                <div class="file-upload-group">
                    <label class="form-label">Resume</label>
                    <input class="form-control" type="file" name="resume" accept=".pdf,.doc,.docx">
                    @if ($employee->resume)
                        <p class="mt-2">Current Resume: <a href="{{ asset('uploads/resumes/' . $employee->resume) }}"
                                target="_blank" class="uploaded-file">View</a></p>
                    @endif
                    @error('resume')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row g-3">
                    <!-- Name & Email -->
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name', $employee->name) }}" placeholder="Enter name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email"
                                value="{{ old('email', $employee->email) }}" placeholder="Enter email">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-4 mt-0">
                        <label class="form-label">Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" name="phone"
                                value="{{ old('phone', $employee->phone) }}" placeholder="Enter phone number">
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Position -->
                    <div class="col-md-4 mt-0">
                        <label class="form-label">Designation</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            <input type="text" class="form-control" name="position"
                                value="{{ old('position', $employee->position) }}" placeholder="Enter position">
                        </div>
                    </div>

                    <!-- Salary -->
                    <div class="col-md-4 mt-0">
                        <label class="form-label">Basic Salary</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                            <input type="text" name="salary" class="form-control"
                                value="{{ old('salary', $employee->salary) }}" placeholder="Enter salary">
                        </div>
                    </div>

                    <!-- Date of Birth & Gender -->
                    <div class="col-md-6 ">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth"
                            value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="gender" value="male"
                                    {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }}> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female"
                                    {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}> Female
                            </label>
                            <label>
                                <input type="radio" name="gender" value="other"
                                    {{ old('gender', $employee->gender) == 'other' ? 'checked' : '' }}> Other
                            </label>
                        </div>
                    </div>

                    <!-- Marital Status & Blood Group -->
                    <div class="col-md-6">
                        <label class="form-label">Marital Status</label>
                        <select class="form-select" name="marital_status">
                            <option value="">Select Status</option>
                            <option value="single"
                                {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single
                            </option>
                            <option value="married"
                                {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>
                                Married</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Blood Group</label>
                        <select class="form-select" name="blood_group">
                            <option value="">Select Blood Group</option>
                            <option value="A+"
                                {{ old('blood_group', $employee->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="B+"
                                {{ old('blood_group', $employee->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="O+"
                                {{ old('blood_group', $employee->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="AB+"
                                {{ old('blood_group', $employee->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address"
                            value="{{ old('address', $employee->address) }}" placeholder="Enter address">
                    </div>

                    <!-- Date of Joining & Leaving -->
                    <div class="col-md-6">
                        <label class="form-label">Date of Joining</label>
                        <input type="date" class="form-control" name="date_of_joining"
                            value="{{ old('date_of_joining', $employee->date_of_joining) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Leaving</label>
                        <input type="date" class="form-control" name="date_of_leaving"
                            value="{{ old('date_of_leaving', $employee->date_of_leaving) }}">
                    </div>

                    <!-- Form Submit -->
                    <div class="form-submit-btn">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update
                            Employee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
