@extends('admin.layouts.layout')


@section('title', 'Lead List')
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="row user-access mb-3">
                <div class="user-welcome col-md-6">
                    <h3>Lead List</h3>
                </div>                

            </div>
           
            <div class="users-datatable mt-3 py-3">
                <div class="row">
                    <div class="col">
                        <div class="users-table">
                            
                                <label for="company_name" class="form-label">Select Company</label>
                                <select name="company_name" id="company_name" class="tomselect">
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" {{ request('company_name') == $company->company_name ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                               
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#company_name').on('change', function () {
                
                const id = $(this).val();
                const url = `{{ route('admin.leadList', ':id') }}`.replace(':id', id);
                window.location.href = url;
            });
        });
    </script>
@endsection
