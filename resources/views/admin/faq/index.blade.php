@extends('admin.layouts.layout')

@section('title', 'FAQs Management')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">FAQs Management</h3>
                <p class="text-muted mb-0">Manage frequently asked questions and related images for STAFO.</p>
            </div>
            <a href="{{ route('faq.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Add New FAQ
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 70px;">#</th>
                                <th style="width: 120px;">Image</th>
                                <th>Category / Question</th>
                                <th style="width: 120px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $index => $faq)
                                <tr>
                                    <td><span class="badge bg-light text-dark">{{ $faqs->firstItem() + $index }}</span></td>
                                    <td>
                                        @if ($faq->image)
                                            <div class="position-relative">
                                                <img src="{{ asset('uploads/faq/' . $faq->image) }}"
                                                    alt="FAQ Image" class="rounded border shadow-sm"
                                                    style="width: 70px; height: 50px; object-fit: cover;">
                                            </div>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $faq->question }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 500px;">
                                            {{ strip_tags($faq->answer) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('faq.edit', $faq->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" onclick="confirmDelete(event, {{ $faq->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $faq->id }}" action="{{ route('faq.destroy', $faq->id) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No FAQs found. Click "Add New FAQ" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($faqs->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $faqs->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(event, faqId) {
            event.preventDefault();

            Swal.fire({
                title: 'Delete this FAQ?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${faqId}`).submit();
                }
            });
        }
    </script>
@endsection