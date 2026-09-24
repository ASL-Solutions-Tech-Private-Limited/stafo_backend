@extends('user.layouts.app')

@section('title', 'Record Employee KPI Performance | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Record Performance: {{ $employees->name }}</h3>
                <p class="text-muted small mb-0">Enter monthly evaluation scores for all active performance KPIs</p>
            </div>
            <a href="{{ route('employeeRankList') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Rankings
            </a>
        </div>

        <form action="{{ route('saveEmployeePerformance', $employees->id) }}" method="POST">
            @csrf

            <!-- Month & Year Row -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="month" class="form-label fw-semibold text-dark">
                            <i class="fa-regular fa-calendar-days text-primary me-1"></i> Evaluation Month <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="month" id="month" required>
                            <option value="">-- Select Month --</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>                           
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="year" class="form-label fw-semibold text-dark">
                            <i class="fa-solid fa-calendar text-primary me-1"></i> Evaluation Year <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="year" id="year" required>
                            <option value="">-- Select Year --</option>
                            @for($yr = 2023; $yr <= date('Y') + 5; $yr++)
                                <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endfor
                        </select>                           
                    </div>
                </div>
            </div>

            <!-- KPI Metric Inputs -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sliders text-primary"></i> KPI Scores (Points)
                </h5>
                <div class="row g-3">
                    @foreach($Performancetypes as $Performancetype)
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 border">
                                <label for="performence_type_{{$Performancetype->id}}" class="form-label fw-semibold text-dark">
                                    {{ $Performancetype->name }}
                                </label>
                                @if($Performancetype->description)
                                    <small class="text-muted d-block mb-2">{{ $Performancetype->description }}</small>
                                @endif
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-star text-warning"></i></span>
                                    <input type="number" step="0.1" name="performence_type_{{$Performancetype->id}}" id="performence_type_{{$Performancetype->id}}" class="form-control" placeholder="Enter score (e.g. 85)">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row" id="salary_section"></div>
                 
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('employeeRankList') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Performance Scores
                </button>
            </div>
        </form>
    </div>
</div>
@endsection