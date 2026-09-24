@extends('user.layouts.app')
@section('title', 'Tasks & Projects | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Tasks & Project Milestones</h3>
                <p class="text-muted small mb-0">Assign tasks, track due dates, set priorities, and collaborate on comments</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('taskCreate') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Create Task
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>   
                        <th>Task Title</th>
                        <th style="width: 160px;">Start Date</th>
                        <th style="width: 160px;">Due Date</th>
                        <th style="width: 130px;" class="text-center">Priority</th>
                        <th style="width: 140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $index => $task)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-list-check"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $task->title }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="fa-regular fa-calendar-plus text-primary me-1"></i>
                                    {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d M, Y') : '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="fa-regular fa-calendar-check text-danger me-1"></i>
                                    {{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d M, Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $prioClass = 'badge-stafo-info';
                                    $prio = strtolower($task->priority ?? '');
                                    if ($prio == 'high' || $prio == 'urgent') $prioClass = 'badge-stafo-danger';
                                    elseif ($prio == 'medium') $prioClass = 'badge-stafo-warning';
                                    elseif ($prio == 'low') $prioClass = 'badge-stafo-success';
                                @endphp
                                <span class="badge-stafo {{ $prioClass }}">
                                    <i class="fa-solid fa-flag me-1"></i> {{ ucfirst($task->priority ?: 'Normal') }}
                                </span>
                            </td>                                
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('commentList', $task->id) }}" 
                                       class="btn btn-sm btn-outline-info p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Comments / Discussion">
                                        <i class="fa-solid fa-comments"></i>
                                    </a>

                                    <a href="{{ route('taskEdit', $task->id) }}" 
                                       class="btn btn-sm btn-outline-warning p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Edit Task">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger p-0" 
                                            style="width: 32px; height: 32px; border-radius: 8px;"
                                            title="Delete Task"
                                            onclick="confirmDelete(event, {{ $task->id }})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>

                                    <form id="delete-form-{{ $task->id }}"
                                        action="{{ route('taskDelete', $task->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-list-check fs-2 mb-2 d-block opacity-50"></i>
                                No tasks found. Click "Create Task" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, taskId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this task!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${taskId}`).submit();
            }
        });
    }
</script>
@endsection