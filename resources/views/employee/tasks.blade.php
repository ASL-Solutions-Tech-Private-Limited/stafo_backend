@extends('employee.layouts.app')

@section('title', 'My Tasks | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Assigned Tasks</h4>
            <span class="text-muted small">View and manage tasks assigned to you by your team manager</span>
        </div>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- Task Workflow & Priority Guidelines Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-list-check"></i>
            <span>Task Pulse</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <span class="marquee-chip chip-task">
                    <i class="fa-solid fa-fire text-danger"></i>
                    <strong>High Priority:</strong> Please prioritize tasks with critical deadlines and keep status updated in real time.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-spinner text-success"></i>
                    <strong>Status Workflow:</strong> Switch tasks from 'Pending' to 'In Progress' when beginning execution.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-payroll">
                    <i class="fa-solid fa-circle-check text-info"></i>
                    <strong>Task Completion:</strong> Mark tasks as 'Done' upon final review by your reporting lead.
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-comments text-primary"></i>
                    <strong>Collaboration:</strong> Reach out to your team supervisor if you need deadline extensions or clarification.
                </span>
            </marquee>
        </div>
        <div class="d-none d-md-flex align-items-center text-muted small ps-2 border-start" style="font-size: 0.72rem; white-space: nowrap;">
            <i class="fa-solid fa-hand-pointer text-warning me-1"></i> Hover to pause
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-check me-2 text-primary"></i> Tasks List
            </h6>
            <span class="badge bg-light text-muted border small">{{ $tasks->total() }} Tasks</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Task Title</th>
                            <th>Priority</th>
                            <th>Timeline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>
                                    <h6 class="fw-bold text-dark mb-1 small">{{ $task->title }}</h6>
                                    @if($task->description)
                                        <p class="text-muted small mb-0" style="max-width: 320px;">{{ Str::limit($task->description, 70) }}</p>
                                    @endif
                                </td>
                                <td>
                                    @php $priority = strtolower($task->priority ?? 'medium'); @endphp
                                    @if($priority === 'high')
                                        <span class="badge bg-danger bg-opacity-15 text-danger"><i class="fa-solid fa-angles-up me-1"></i> High</span>
                                    @elseif($priority === 'low')
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary">Low</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-15 text-warning">Medium</span>
                                    @endif
                                </td>
                                <td class="small">
                                    <div class="text-dark fw-semibold">
                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                        Due: {{ $task->end_date ? Carbon\Carbon::parse($task->end_date)->format('d M, Y') : 'Ongoing' }}
                                    </div>
                                    @if($task->start_date)
                                        <small class="text-muted">Start: {{ Carbon\Carbon::parse($task->start_date)->format('d M, Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($task->status === 'completed')
                                        <span class="badge bg-success bg-opacity-15 text-success"><i class="fa-solid fa-check me-1"></i> Completed</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge bg-info bg-opacity-15 text-info"><i class="fa-solid fa-spinner fa-spin me-1"></i> In Progress</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-15 text-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('employee.task.updateStatus', $task->id) }}" method="POST" class="d-inline-flex align-items-center gap-1">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" style="width: 125px;" onchange="this.form.submit()">
                                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">No tasks currently assigned to you.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tasks->hasPages())
                <div class="p-3 border-top">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
