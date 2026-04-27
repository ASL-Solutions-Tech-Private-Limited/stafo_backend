@extends('user.layouts.app')

@section('title', 'Followup Edit') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Followup Edit</h2>
            <form action="{{ route('followupUpdate',$followup->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="type">Type</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type',$followup->type) }}"
                                required>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>                    
                    

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <input type="text" name="status" class="form-control" value="{{ old('status',$followup->status) }}"
                                >
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="next_date">Next Date</label>
                            <input type="date" name="next_date" class="form-control" value="{{ old('next_date',$followup->next_date) }}"
                                required>
                            @error('next_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="4">{{ old('remarks',$followup->remarks) }}</textarea>
                            @error('remarks')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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