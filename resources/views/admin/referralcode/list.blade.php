@extends('admin.layouts.layout')


@section('title', 'Referralcode List')
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="row user-access mb-3">
                <div class="user-welcome col-md-4">
                    <h3>Referral Code List for </h3>
                </div>
                <div class="col-md-6 ">
                    <select name="company_name" id="company_name" class="tomselect">
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ $company_id == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('createReferralCode',$id) }}" class="btn btn-primary">
                            Add</a>
                </div>

            </div>
            <form method="GET" action="{{ route('referralCodeList',$id) }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="referralcode" class="form-control" placeholder="Search by Name"
                            value="{{ request('referralcode') }}">
                    </div>                    
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn btn-info">Search</button>
                        <a href="{{ route('referralCodeList',$id) }}" class="btn btn-danger ">Reset</a>
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
                                            <th><b>S.N.</b></th>
                                            <th><b>Referral Code</b></th>
                                            <th><b>Start Date</b></th>
                                            <th><b>End Date</b></th>
                                            <th><b>Use Count</b></th>
                                            <th><b>Status</b></th>
                                            <th><b>Action</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($data->isEmpty())
                                            <tr>
                                                <td colspan="7" class="text-center">No records found.</td>
                                            </tr>
                                        @else
                                        @foreach ($data as $key => $item)
                                            <tr>
                                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                                </td>
                                                <td>
                                                    {{ $item->referralcode }}
                                                    @if($item->referrals->count() > 0)
                                                        <i class="fa fa-solid fa-eye" style="font-size:16px;color:green"
                                                            data-toggle="modal" data-target="#verificationModal-{{ $item->id }}"
                                                            title="View referral list"></i>
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="verificationModal-{{ $item->id }}" tabindex="-1"
                                                            role="dialog" aria-labelledby="verificationModalLabel-{{ $item->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="verificationModalLabel-{{ $item->id }}">Referral
                                                                            Details</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                    <table class="table table-bordered table-striped">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Referrer Name</th>
                                                                                <th>Referrer Email</th>
                                                                                <th>Referrer Phone</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($item->referrals as $referrer)
                                                                                <tr>
                                                                                    <td>{{ $referrer->company_name }}</td>
                                                                                    <td>{{ $referrer->email }}</td>
                                                                                    <td>{{ $referrer->mobile_no }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif
                                                </td>
                                                <td>{{ $item->start_date }}</td>
                                                <td>{{ $item->end_date }}</td>
                                                
                                                <td>{{ $item->use_count }}</td>
                                                <td>
                                                    <span
                                                        class="status-toggle {{ $item->status == 1 ? 'text-success' : 'text-danger' }}"
                                                        data-id="{{ $item->id }}" style="cursor: pointer;">
                                                        {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="actions">  
                                                    <a class="edit" href="{{ route('editReferralCode', $item->id) }}"><i
                                                    class="fa fa-solid fa-pen"></i></a>
                                                        <form method="POST" style="display:inline;"
                                                            action="{{ route('destroyReferralCode', $item->id) }}"
                                                            id="delete-form-{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="text-primary"
                                                                style="all:unset; border:none; background:none;"
                                                                onclick="confirmDelete(event, {{ $item->id }})">
                                                                <i class="fa fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                {{ $data->links('vendor.pagination.bootstrap-5') }}
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
                text: "Record will be deleted and you won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#company_name').on('change', function () {
                
                const id = $(this).val();
                const url = `{{ route('referralCodeList', ':id') }}`.replace(':id', id);
                window.location.href = url;
            });
            $('.status-toggle').on('click', function () {
                const id = $(this).data('id');
                const $this = $(this);

                const url = `{{ route('referralcodeStatus', ':id') }}`.replace(':id', id);
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