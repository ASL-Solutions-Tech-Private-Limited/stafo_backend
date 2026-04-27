@extends('user.layouts.app')

@section('title', 'Employee Performance add') <!-- Set your custom title here -->

@section('content')

    @include('user.layouts.alert')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Employee Performance add</h2>
            <form action="{{ route('saveEmployeePerformance', $employees->id) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="month">Month</label>
                             <select class="form-control" name="month" required>
                                <option value ="">Select Month</option>
                                <option value = "1">January</option>
                                <option value = "2">February</option>
                                <option value = "3">March</option>
                                <option value = "4">April</option>
                                <option value = "5">May</option>
                                <option value = "6">June</option>
                                <option value = "7">July</option>
                                <option value = "8">August</option>
                                <option value = "9">September</option>
                                <option value = "10">October</option>
                                <option value = "11">November</option>
                                <option value = "12">December</option>
                             </select>                           
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="year">Year</label>
                             <select class="form-control" name="year" required>
                                <option value ="">Select year</option>
                                <option value = "2023">2023</option>
                                <option value = "2024">2024</option>
                                <option value = "2025">2025</option>
                                <option value = "2026">2026</option>
                                <option value = "2027">2027</option>
                             </select>                           
                        </div>
                    </div>

                    @foreach($Performancetypes as $Performancetype)
                        
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="performence_type">{{ $Performancetype->name }}</label>
                            <input type="text" name="performence_type_{{$Performancetype->id}}" class="form-control"  value="" >
                        </div>
                    </div>
                @endforeach
                    
                    
                </div>
                    <div class="row" id="salary_section"></div>
                     
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
   
@endsection