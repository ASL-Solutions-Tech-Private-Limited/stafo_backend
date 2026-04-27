@extends('user.layouts.app')
@section('title', 'Expense List') <!-- Set your custom title here -->
@section('css')
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">

        <div class="row mb-3">
            <div class="col-md-9 col-6">
                <h2 class="fw-bold">Expense</h2>
            </div>
            <div class="col-md-3 col-6 text-end">
                <a href="{{ route('expenseCreate') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                    Create</a>
            </div>
        </div>
       @include('user.layouts.alert')
        <!-- <form method="GET" action="{{ route('expenseformList') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                        value="{{ request()->get('name') }}">
                </div>
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search "></i>
                        Search</button>
                </div>
            </div>
        </form> -->

        <!-- Reimbursement Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Employee</th>
                        <th>Amount</th>
                        <th>Created On</th>
                        <th>Status</th>
                        
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($expensees as $index => $expense)
                        <tr>
                            <td>{{ $expensees->firstItem() + $index }}</td>
                            <td>{{ $expense->employee->name }}</td>
                            <td>{{ $expense->amount }}</td>
                            <td>{{ $expense->created_at }}</td>
                            <td>
                                {{ $expense->status}}
                                @if($expense->status == 'Pending')
                                    <span class="badge bg-success status_change" title="Approved" data-id="{{ $expense->id }}"><i class="fa fa-check" ></i></span>
                                    <span class="badge bg-danger status_change" title="Rejected" data-id="{{ $expense->id }}"><i class="fa fa-times" ></i></span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>
                                <a href="{{ route('expenseDetails', $expense->id) }}" class="btn btn-info btn-sm"><i
                                        class="fas fa-eye"></i></a>
                                <a href="{{ route('expenseEdit', $expense->id) }}" class="btn btn-warning btn-sm"><i
                                        class="fas fa-edit"></i></a>
                                <form action="{{ route('expenseDelete', $expense->id) }}" method="POST"
                                    style="display:inline;" id="delete-form-{{ $expense->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, {{ $expense->id }})"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $expensees->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
    </div>

@endsection
@section('js')    

    <script>

        $(document).ready(function() {
            $('.status_change').on('click', function() {
                var status = $(this).attr('title');
                var expenseId = $(this).attr('data-id');
                Swal.fire({
                    title: 'Change Status',
                    text: `Are you sure you want to change the status to ${status}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('expensestatuschange') }}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status,
                                id: expenseId
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Changed!',
                                    `Status has been changed to ${status}.`,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error changing the status.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
        function confirmDelete(event, reimbursementId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${reimbursementId}`).submit();
                }
            });
        }
    </script>
@endsection