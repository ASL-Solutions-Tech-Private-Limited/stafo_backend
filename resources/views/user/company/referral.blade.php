@extends('user.layouts.app')
@section('title', 'Company Documents Verification') <!-- Set your custom title here -->

@section('css')
    <style>
        /* Apply Roboto font to the entire page */
    </style>
@endsection
@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="docs-data">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold">Referrals</h2>
                </div>


                <!-- Upload Image -->

                <div class="row g-3">
                    <label class="form-label fw-semibold">Your Code: {{ $company->referral_code }}</label> 

                </div>
                <div class="row g-3">
                    <label class="form-label fw-semibold">Your referrer : {{ $rcount }}</label> 
                    <div class="col-md-6">
                        @if($rlist->count() > 0)
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Referrer Name</th>
                                        <th>Referrer Email</th>
                                        <th>Referrer Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rlist as $referrer)
                                        <tr>
                                            <td>{{ $referrer->company_name }}</td>
                                            <td>{{ $referrer->email }}</td>
                                            <td>{{ $referrer->mobile_no }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No referrer found.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>





    </div>
@endsection

