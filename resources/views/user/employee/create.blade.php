@extends('user.layouts.app')

@section('title', 'Employee add') <!-- Set your custom title here -->


@section('content')
    <div class="card mt-4 p-3">

        {{-- <h1>Add Employee</h1> --}}


        <div class="continer">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Employee</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('employee.index') }}" class="btn btn-primary" style="float: right;">Employee
                        List</a>
                </div>
            </div>

            <form action="{{ route('user.employees.store') }}" method="POST">
                @csrf
                <div class="row align-items-end">

                    <!-- Name Input -->
                    <div class="col-md-6 mb-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="col-md-6 mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Input -->
                    <div class="col-md-6 mb-3">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                            maxlength="10">
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Position Input -->
                    <div class="col-md-6 mb-3">
                        <label for="position">Designation</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}"
                            placeholder="e.g. HR Manager">
                    </div>


                    <!-- Salary Input -->
                    <div class="col-md-6 mb-3">
                        <label for="salary">Basic Salary</label>
                        <input type="number" name="salary" class="form-control" value="{{ old('salary') }}"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                    </div>

                    <!-- Save Button -->
                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" class="btn btn-success ">Save</button>
                    </div>
                </div>
            </form>


        </div>




    </div>
@endsection
