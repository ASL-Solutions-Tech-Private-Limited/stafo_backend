<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $type;
    public $punchTime;
    public $punchDate;
    public $method;

    /**
     * Create a new message instance.
     *
     * @param mixed $employee
     * @param string $type ('punch_in' | 'punch_out')
     * @param string|null $punchTime
     * @param string|null $punchDate
     * @param string|null $method
     */
    public function __construct($employee, string $type = 'punch_in', ?string $punchTime = null, ?string $punchDate = null, ?string $method = 'Selfie Attendance')
    {
        $this->employee = $employee;
        $this->type = $type;
        $this->punchTime = $punchTime ?: now()->format('h:i A');
        $this->punchDate = $punchDate ?: now()->format('d M, Y');
        $this->method = $method ?: 'Attendance System';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $actionTitle = $this->type === 'punch_in' ? 'Punch In Recorded' : 'Punch Out Recorded';
        $subject = "{$actionTitle} - " . ($this->employee->name ?? 'Employee') . " (" . $this->punchDate . ")";

        $branchName = null;
        $departmentName = null;

        try {
            if ($this->employee && method_exists($this->employee, 'branch') && $this->employee->branch) {
                $branchName = $this->employee->branch->name ?? null;
            }
            if ($this->employee && method_exists($this->employee, 'department') && $this->employee->department) {
                $departmentName = $this->employee->department->name ?? null;
            }
        } catch (\Throwable $e) {}

        return $this->subject($subject)
            ->view('emails.attendance_notification', [
                'employeeName'   => $this->employee->name ?? 'Employee',
                'employeeId'     => $this->employee->id ?? '',
                'employeeCode'   => $this->employee->emp_id ?? ('#' . ($this->employee->id ?? '')),
                'type'           => $this->type,
                'punchTime'      => $this->punchTime,
                'punchDate'      => $this->punchDate,
                'method'         => $this->method,
                'branchName'     => $branchName,
                'departmentName' => $departmentName,
            ]);
    }
}
