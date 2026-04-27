@extends('user.layouts.app')
@section('title', 'Employee Rank List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <form action="{{ route('employeeRankList') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-4 col-6">
                        <h2 class="fw-bold">Employee Rank</h2>
                    </div>             
                
                    <div class="col-md-2">
                        <select name="month" class="form-select mb-2">
                            <option value="1" @if($month == 1) selected @endif>January</option>
                            <option value="2" @if($month == 2) selected @endif>February</option>
                            <option value="3" @if($month == 3) selected @endif>March</option>
                            <option value="4" @if($month == 4) selected @endif>April</option>
                            <option value="5" @if($month == 5) selected @endif>May</option>
                            <option value="6" @if($month == 6) selected @endif>June</option>
                            <option value="7" @if($month == 7) selected @endif>July</option>
                            <option value="8" @if($month == 8) selected @endif>August</option>
                            <option value="9" @if($month == 9) selected @endif>September</option>
                            <option value="10" @if($month == 10) selected @endif>October</option>
                            <option value="11" @if($month == 11) selected @endif>November</option>
                            <option value="12" @if($month == 12) selected @endif>December</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select mb-2">
                            @for($yr = 2023; $yr <= date('Y') + 10; $yr++)
                                <option value="{{ $yr }}" @if($year == $yr) selected @endif>{{ $yr }}</option>  
                            @endfor                          
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="employee_id" class="form-select mb-2">
                            <option value="">Select Employee</option>
                            @if(!$employees->isEmpty())
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @if($employee_id == $employee->id) selected @endif>{{ $employee->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search "></i> Search</button>  
                    </div>                  
                
                </div>
            </form>
            </div>
            <div class="table-responsive table-same">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Rank</th>
                            <th>Employee</th>
                            <th>Phone</th>
                            <th>Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employeeLists as $index => $employee)
                            <tr>                                
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $employee->employee->name }}</td>
                                <td>{{ $employee->employee->phone }}</td>
                                <td>{{ $employee->total_marks }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="showDetailsModal({{ $employee->employee_id }})">View Details</button>                                    
                                    
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event, Id) {
            event.preventDefault(); // Prevent form submission

            // Show SweetAlert confirmation dialog
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
                    // If confirmed, submit the delete form
                    document.getElementById(`delete-form-${Id}`).submit();
                }
            });
        }
    </script>

    <script>
        function showDetailsModal(employeeId) {
            $.ajax({
                url: '{{ route("employeeRankDetails") }}',
                method: 'GET',
                data: { employee_id: employeeId,month: '{{$month}}', year: '{{$year}}' },
                success: function(response) {
                    // Create and show the modal
                    const modalHtml = `
                        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailsModalLabel">Point Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${response.html}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(modalHtml);
                    $('#detailsModal').modal('show');

                    // Remove modal from DOM after it's hidden
                    $('#detailsModal').on('hidden.bs.modal', function () {
                        $(this).remove();
                    });
                },
                error: function() {
                    alert('Failed to fetch employee details.');
                }
            });
        }
    </script>
@endsection