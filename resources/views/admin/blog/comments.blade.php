@extends('admin.layouts.layout')
@section('title', 'Blog Comments')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Blog Comments</h3>
           
        </div>
    
    
    <div class="table-responsive table-same">
    <table class="table table-bordered table-hover">
        <thead  class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comments as $index=>$comment)
            <tr>
                <td>{{ $comments->firstItem() + $index }}</td>
                <td>{{ $comment->name }}</td>
                <td>{{ $comment->email }}</td>
                <td>{{ $comment->comment }}</td>
                <td>
                    <div class="actions">
                        <form action="{{ route('admin.blogcommentdelete', $comment->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $comment->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="prop-none" onclick="confirmDelete(event, {{ $comment->id }})"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No comments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
            {{ $comments->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
</div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(event, Id) {
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
                    document.getElementById(`delete-form-${Id}`).submit();
                }
            });
        }
    </script>

@endsection