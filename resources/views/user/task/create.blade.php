@extends('user.layouts.app')

@section('title', 'New Task') <!-- Set your custom title here -->
@section('css')
<link href="{{ asset('css/tom-select.css') }}" rel="stylesheet">
@endsection
@section('content')

    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">New Task</h2>
            <form action="{{ route('taskStore') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="task_assign ">Assign To</label>
                        <select name="task_assign[]" class="form-control tomselect" multiple required>
                            @if(count($employees) > 0)
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" >
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
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}"
                                required>
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                required>
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="priority">Priority</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                            @error('priority')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="description">Uploads files</label>
                            <input type="file" class="form-control" name="files" multiple>
                        </div>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
<script src="{{ asset('js/tom-select.complete.min.js') }}"></script>

<script>
    new TomSelect('.tomselect', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
</script>
@endsection