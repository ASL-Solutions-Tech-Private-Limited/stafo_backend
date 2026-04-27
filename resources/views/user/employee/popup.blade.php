@extends('user.layouts.app')



@section('content')
    <div class="card mt-4 p-3">

        <!-- Modal to show if Branch or Department doesn't exist -->
        <div class="modal fade" id="branchDepartmentModal" tabindex="-1" aria-labelledby="branchDepartmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="branchDepartmentModalLabel">Action Required</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        You need to create a branch and department before adding employees. Please create them first.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('branche.create') }}" class="btn btn-primary">Create Branch</a>
                        <a href="{{ route('departments.create') }}" class="btn btn-primary">Create Department</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        // If 'showModal' is true, show the modal on page load
        @if ($showModal)
            var myModal = new bootstrap.Modal(document.getElementById('branchDepartmentModal'));
            myModal.show();
        @endif
    </script>
@endsection
