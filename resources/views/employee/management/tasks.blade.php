@extends('employee.layouts.app')

@section('title', 'Team Tasks | Management Portal')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1">
                    <i class="fa-solid fa-list-check me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Team Task Operations</span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">Assign & Oversee Team Tasks</h3>
            <p class="text-muted small mb-0">Create new tasks, delegate to company employees, and monitor progress.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(Auth::guard('employee')->user()->hasPermission('tasks.create'))
                <button type="button" class="btn btn-primary fw-bold rounded-3 shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="fa-solid fa-plus-circle me-1"></i> Create & Assign Task
                </button>
            @endif
        </div>
    </div>

    <!-- Task KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--bs-card-bg, #ffffff);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Company Tasks</small>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalTasks }}</h3>
                    </div>
                    <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                        <i class="fa-solid fa-list-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--bs-card-bg, #ffffff);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Pending Tasks</small>
                        <h3 class="fw-bold mb-0 text-warning">{{ $pendingTasks }}</h3>
                    </div>
                    <div class="rounded-3 p-2 bg-warning-subtle text-warning">
                        <i class="fa-solid fa-clock fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: var(--bs-card-bg, #ffffff);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Completed Tasks</small>
                        <h3 class="fw-bold mb-0 text-success">{{ $completedTasks }}</h3>
                    </div>
                    <div class="rounded-3 p-2 bg-success-subtle text-success">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task List -->
    <div class="row g-3">
        @forelse($tasks as $t)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between" style="background: var(--bs-card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge {{ $t->priority == 'Urgent' || $t->priority == 'High' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-info-subtle text-info border border-info-subtle' }} rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                                    {{ $t->priority }} Priority
                                </span>
                                <span class="badge {{ $t->status == 'completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </div>
                            @php
                                $canEditTask = Auth::guard('employee')->user()->hasPermission('tasks.edit');
                                $canDeleteTask = Auth::guard('employee')->user()->hasPermission('tasks.delete');
                            @endphp
                            @if($canEditTask || $canDeleteTask)
                                <div class="d-flex align-items-center gap-1">
                                    @if($canEditTask)
                                        <button type="button" class="btn btn-sm btn-outline-warning p-0" title="Edit Task" data-bs-toggle="modal" data-bs-target="#editTaskModal_{{ $t->id }}" style="width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endif
                                    @if($canDeleteTask)
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Task" onclick="confirmDeleteTask(event, {{ $t->id }})" style="width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-task-form-{{ $t->id }}" action="{{ route('employee.management.task.destroy', $t->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <h5 class="fw-bold text-dark mb-2">{{ $t->title }}</h5>
                        <p class="text-muted small mb-3" style="font-size: 0.82rem;">
                            {{ Str::limit($t->description ?? 'No extra description provided.', 120) }}
                        </p>

                        <div class="d-flex align-items-center gap-2 text-muted small mb-3" style="font-size: 0.75rem;">
                            <span><i class="fa-solid fa-calendar-day me-1"></i> Due: {{ $t->end_date ? \Carbon\Carbon::parse($t->end_date)->format('d M, Y') : 'Open' }}</span>
                        </div>

                        <div class="pt-3 border-top">
                            <small class="text-muted fw-bold d-block mb-1.5" style="font-size: 0.72rem;">ASSIGNED STAFF:</small>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($t->assignedEmployees as $aEmp)
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-2" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-user me-1 text-primary"></i> {{ $aEmp->name }}
                                    </span>
                                @empty
                                    <span class="text-muted small fst-italic">Unassigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background: var(--bs-card-bg, #ffffff);">
                    <i class="fa-solid fa-clipboard-list text-muted mb-3" style="font-size: 3rem; opacity: 0.4;"></i>
                    <h5 class="fw-bold text-dark">No Tasks Available</h5>
                    <p class="text-muted small mb-3">Click "Create & Assign Task" to delegate tasks to your company teammates.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($tasks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>

<!-- Create Task Modal -->
@if(Auth::guard('employee')->user()->hasPermission('tasks.create'))
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus-circle fs-5"></i>
                    <h5 class="modal-title fw-bold" id="createTaskModalLabel">Assign New Team Task</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('employee.management.task.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Task Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Complete quarterly documentation review" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Provide instructions and expected deliverables..."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-dark">Due Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark">Assign to Employee(s) <span class="text-danger">*</span></label>
                        <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            <div class="row g-2">
                                @foreach($assignableEmployees as $empOpt)
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="employee_ids[]" value="{{ $empOpt->id }}" id="empCheck_{{ $empOpt->id }}">
                                            <label class="form-check-label small" for="empCheck_{{ $empOpt->id }}">
                                                <strong>{{ $empOpt->name }}</strong>
                                                <span class="text-muted">({{ $empOpt->designation->name ?? 'Staff' }})</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fa-solid fa-paper-plane me-1"></i> Assign Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals: Edit Task (guarded by tasks.edit) -->
@if(Auth::guard('employee')->user()->hasPermission('tasks.edit'))
    @foreach ($tasks as $t)
    <div class="modal fade" id="editTaskModal_{{ $t->id }}" tabindex="-1" aria-labelledby="editTaskModalLabel_{{ $t->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square fs-5"></i>
                        <h5 class="modal-title fw-bold" id="editTaskModalLabel_{{ $t->id }}">Edit Team Task</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.management.task.update', $t->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $t->title }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $t->description }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="Low" {{ $t->priority == 'Low' ? 'selected' : '' }}>Low Priority</option>
                                    <option value="Medium" {{ $t->priority == 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                                    <option value="High" {{ $t->priority == 'High' ? 'selected' : '' }}>High Priority</option>
                                    <option value="Urgent" {{ $t->priority == 'Urgent' ? 'selected' : '' }}>Urgent Priority</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $t->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $t->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $t->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@if(Auth::guard('employee')->user()->hasPermission('tasks.delete'))
<script>
    function confirmDeleteTask(event, taskId) {
        event.preventDefault();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This team task will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-task-form-${taskId}`).submit();
                }
            });
        } else {
            if (confirm("Are you sure you want to delete this task?")) {
                document.getElementById(`delete-task-form-${taskId}`).submit();
            }
        }
    }
</script>
@endif
@endsection
