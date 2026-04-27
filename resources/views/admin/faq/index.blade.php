@extends('admin.layouts.layout')

@section('title', 'FAQs Management')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>FAQs Lists</h3>
            <a href="{{ route('faq.create') }}" class="btn btn-primary">Create Faqs</a>
        </div>

        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Question</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faqs as $index => $faq)
                        <tr>
                            <td>{{ $faqs->firstItem() + $index }}</td>
                            <td>{{ $faq->question }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('faq.edit', $faq->id) }}"><i class="fa fa-solid fa-pen"></i></a>

                                    <button onclick="confirmDelete(event, {{ $faq->id }})" class="prop-none">
                                        <i class="fa fa-solid fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $faq->id }}" action="{{ route('faq.destroy', $faq->id) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $faqs->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Include SweetAlert2 script -->

    <script>
        function confirmDelete(event, faqId) {
            event.preventDefault(); // Prevent the form from submitting immediately

            // SweetAlert confirmation box
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
                    // Submit the form if confirmed
                    document.getElementById(`delete-form-${faqId}`).submit();
                }
            });
        }
    </script>
@endsection