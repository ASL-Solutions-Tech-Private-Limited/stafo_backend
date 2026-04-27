@extends('admin.layouts.layout')
@section('title', 'Blog')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Blog</h3>
           <a href="{{ route('admin.blog.create') }}" class="btn btn-primary mb-3">Add New Blog</a>
        </div>
    
    
    <div class="table-responsive table-same">
    <table class="table table-bordered table-hover">
        <thead  class="table-dark">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Auther</th>
                <th>Short Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($blogs as $index=>$blog)
            <tr>
                <td>{{ $blogs->firstItem() + $index }}</td>
                <td>{{ $blog->title }}</td>
                <td>{{ $blog->auther }}</td>
                <td>{{ $blog->short_description }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.blogcomments', $blog->id) }}"><i class="fas fa-comments"></i></a>
                         <a href="{{ route('admin.blog.edit', $blog->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.blog.destroy', $blog->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $blog->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="prop-none" onclick="confirmDelete(event, {{ $blog->id }})"><i class="fas fa-trash"></i></button>
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
            {{ $blogs->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
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