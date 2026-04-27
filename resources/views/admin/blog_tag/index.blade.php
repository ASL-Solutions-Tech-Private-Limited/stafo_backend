@extends('admin.layouts.layout')
@section('title', 'Blog Tag')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Blog Tags</h3>
           <a href="{{ route('admin.blog_tag.create') }}" class="btn btn-primary mb-3">Add New Tag</a>
        </div>
    
    
    <div class="table-responsive table-same">
    <table class="table table-bordered table-hover">
        <thead  class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $index=>$tag)
            <tr>
                <td>{{ $tags->firstItem() + $index }}</td>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->created_at->format('Y-m-d') }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.blog_tag.edit', $tag->id) }}" ><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.blog_tag.destroy', $tag->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $tag->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="prop-none" onclick="confirmDelete(event, {{ $tag->id }})"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No tags found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
            {{ $tags->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
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