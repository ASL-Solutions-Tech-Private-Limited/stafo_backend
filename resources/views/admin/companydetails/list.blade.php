@extends('admin.layouts.layout')


@section('title', 'Company List')
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="row user-access mb-3">
                <div class="user-welcome col-md-6">
                    <h3>Company Details</h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('company.details.create') }}" class="btn btn-primary">
                            Add</a>
                </div>

            </div>
            <form method="GET" action="{{ route('company.details.list') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="company_name" id="company_name" class="tomselect">
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_name }}" {{ request('company_name') == $company->company_name ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="mobile_no" class="form-control" placeholder="Search by Phone"
                            value="{{ request('mobile_no') }}">
                    </div>
                    <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control" placeholder="Registration Date From"
                            value="{{ request('from_date') }}">
                            <i>Select From Date</i>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" placeholder="Registration Date To"
                            value="{{ request('to_date') }}">
                        <i>Select To Date</i>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="submit" class="btn btn-info">Search</button>
                        <a href="{{ route('company.details.list') }}" class="btn btn-danger ">Reset</a>
                    </div>

                </div>
            </form>
            <div class="users-datatable mt-3 py-3">
                <div class="row">
                    <div class="col">
                        <div class="users-table">
                            <div class="table-responsive table-same">
                                <table id="usersTable" class="table w-100 table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Company Name</th>
                                            <th>Phone</th>
                                            <!-- <th><b>GST Number</b></th>
                                            <th><b>PAN Number</b></th> -->
                                            <th>Registered On</th>
                                            <!-- <th><b>Referrals Code</b></th>
                                            <th><b>Referrals</b></th> -->
                                            <th>Used Referral Code</th>
                                            <th>Max Employee Add</th>
                                            <th>Employee Added</b></th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $item)
                                            <tr>
                                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                                </td>
                                                <td>
                                                    {{ $item->company_name }}
                                                    @if($item->is_verified == 'Yes')
                                                        <i class="fa fa-solid fa-check-circle" style="font-size:16px;color:green"
                                                            data-toggle="modal" data-target="#verificationModal-{{ $item->id }}"
                                                            title="View Aadhar Details"></i>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="verificationModal-{{ $item->id }}" tabindex="-1"
                                                            role="dialog" aria-labelledby="verificationModalLabel-{{ $item->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="verificationModalLabel-{{ $item->id }}">Aadhar
                                                                            Details</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @php
                                                                            $aadharDetails = json_decode($item->aadhar_response, true);
                                                                           
                                                                        @endphp

                                                                        <table class="aadhar_table">
                                                                            <tr>
                                                                                <td>&nbsp;</td>
                                                                                <td>&nbsp;</td>
                                                                                <td style="align:center;">
                                                                                    @if (isset($aadharDetails['data']['profile_image']))
                                                                                        
                                                                                    <img
                                                                                        src="data:image/jpeg;base64,{{ $aadharDetails['data']['profile_image'] }}"
                                                                                        alt="Aadhar Image" />
                                                                                    @endif

                                                                                    
                                                                                    </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Number</strong></td>
                                                                                <td>: </td>
                                                                                <td>
                                                                                    @if (isset($aadharDetails['data']['aadhaar_number']))
                                                                                        {{ $aadharDetails['data']['aadhaar_number'] }}
                                                                                    {{ substr_replace($aadharDetails['data']['aadhaar_number'], str_repeat('*', 8), 0, 8) }}
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Name</strong></td>
                                                                                <td>: </td>
                                                                                <td>
                                                                                    @if (isset($aadharDetails['data']['full_name']))
                                                                                        {{ $aadharDetails['data']['full_name'] }}
                                                                                    {{ $aadharDetails['data']['full_name'] }}
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="vertical-align: top;">
                                                                                    <strong>Address</strong>
                                                                                </td>
                                                                                <td style="vertical-align: top;">: </td>
                                                                                <td>
                                                                                @if (isset($aadharDetails['data']))

                                                                                    {{ $aadharDetails['data']['address']['house'] }},
                                                                                    {{ $aadharDetails['data']['address']['street'] }},
                                                                                    {{ $aadharDetails['data']['address']['landmark'] }},
                                                                                    {{ $aadharDetails['data']['address']['loc'] }},
                                                                                    {{ $aadharDetails['data']['address']['po'] }},
                                                                                    {{ $aadharDetails['data']['address']['vtc'] }},
                                                                                    {{ $aadharDetails['data']['address']['subdist'] }},
                                                                                    {{ $aadharDetails['data']['address']['dist'] }},
                                                                                    {{ $aadharDetails['data']['address']['state'] }},
                                                                                    {{ $aadharDetails['data']['address']['country'] }},
                                                                                    {{ $aadharDetails['data']['zip'] }}

                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>DOB</strong></td>
                                                                                <td>: </td>
                                                                                <td>
                                                                                  @if (isset($aadharDetails['data']['dob']))
                                                                                    {{ $aadharDetails['data']['dob'] }}
                                                                                    
                                                                                    @endif  
                                                                               </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><strong>Gender</strong></td>
                                                                                <td>: </td>
                                                                                <td>
                                                                                @if (isset($aadharDetails['data']['gender'])) 
                                                                                {{ $aadharDetails['data']['gender'] }}
                                                                                @endif
                                                                            </td>
                                                                            </tr>
                                                                        </table>

                                                                        <!-- Add more verification details here if needed -->
                                                                    </div>
                                                                    <!-- <div class="modal-footer">
                                                                                                                                                                                                    <button type="button" class="btn btn-close"
                                                                                                                                                                                                        data-dismiss="modal">Close</button>
                                                                                                                                                                                                </div> -->
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif
                                                </td>
                                                <td>{{ $item->mobile_no }}</td>
                                                <!-- <td>{{ $item->gst_number }}</td>
                                                <td>{{ $item->pan_number }}</td> -->
                                                <td>{{ date('dS F, Y', strtotime($item->created_at)) }}</td>
                                                <!-- <td>{{ $item->referral_code }}</td>
                                                <td>{{ $item->referrals->count() }}</td> -->
                                                <td>{{ $item->referralcode_used }}</td>
                                                <td>{{ $item->max_employee_add }}</td>
                                                <td>{{ $item->employee_added }}</td>
                                                <td>
                                                    <span
                                                        class="status-toggle {{ $item->status == 1 ? 'text-success' : 'text-danger' }}"
                                                        data-id="{{ $item->id }}" style="cursor: pointer;">
                                                        {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="actions">
                                                        <a href="{{ route('company.details.show', $item->id) }}" class="view" title="View Company details">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('referralCodeList', $item->id) }}" class="view" title="View Referral">
                                                            <i class="fa fa-user-plus"></i>
                                                        </a>
                                                        <a class="edit" href="{{ route('company.details.edit', $item->id) }}"><i
                                                                class="fa fa-solid fa-pen"></i></a>
                                                        <button class="text-primary"
                                                                style="all:unset; border:none; background:none;"
                                                                onclick="confirmDelete(event, {{ $item->id }})">
                                                                <i class="fa fa-solid fa-trash"></i>
                                                        </button>
                                                        <div class="modal fade" id="deleteModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-{{ $item->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="deleteModalLabel-{{ $item->id }}">Confirm Deletion</h5>
                                                                        
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete this company? All related data will be permanently removed and cannot be recovered.
                                                                    </div>
                                                                    <div class="modal-body">
                                                                    
                                                                        <form method="POST" style="display:inline;" action="{{ route('company.details.destroy', $item->id) }}" id="delete-form-{{ $item->id }}">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <input type="password" name="pin" id="pin_{{ $item->id }}" class="form-control" placeholder="Enter PIN" required>
                                                                            <button type="button" class="btn btn-secondary" onclick="$('#deleteModal-{{ $item->id }}').modal('hide');">Cancel</button>
                                                                            <button type="button" class="btn btn-danger" onclick="ajaxConfirmDelete(event, {{ $item->id }})">Delete</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                {{ $data->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(event, itemId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "All the related data like Employee, Branch, Department, Shift, Chats etc will be deleted and you won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    $('#deleteModal-' + itemId).modal('show');
                    //document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        }
    </script>
    <script>

        function ajaxConfirmDelete(event, itemId) {
            //const url = `{{ route('company.status.toggle', ':id') }}`.replace(':id', id);
            const url = `{{ route('pinchecking') }}`; 
            const pin = document.getElementById(`pin_${itemId}`).value;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pin: pin,
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            document.getElementById(`delete-form-${itemId}`).submit();
                        } else {
                            alert('Invalid Pin. Please try again.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('An error occurred while updating the status.');
                    }
                });
        }
        $(document).ready(function () {
            $('.status-toggle').on('click', function () {
                const id = $(this).data('id');
                const $this = $(this);

                const url = `{{ route('company.status.toggle', ':id') }}`.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        if (response.success) {
                            $this.text(response.new_status === 1 ? 'Active' : 'Inactive')
                                .toggleClass('text-success', response.new_status === 1)
                                .toggleClass('text-danger', response.new_status === 0);
                        } else {
                            alert('Failed to update status.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('An error occurred while updating the status.');
                    }
                });
            });
        });
        
    </script>
@endsection