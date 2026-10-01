<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MissedPunchOutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $targetDate;
    public $formattedDate;
    public $inTime;
    public $company;
    public $companyName;
    public $companyEmail;

    /**
     * Create a new message instance.
     *
     * @param mixed $employee
     * @param string|null $targetDate (format Y-m-d)
     * @param string|null $inTime
     */
    public function __construct($employee, ?string $targetDate = null, ?string $inTime = null)
    {
        $this->employee = $employee;
        $this->targetDate = $targetDate ?: date('Y-m-d');
        $this->formattedDate = Carbon::parse($this->targetDate)->format('d M, Y (D)');
        $this->inTime = $inTime ?: null;

        // Company Details
        $this->company = $employee->company ?? null;
        $this->companyName = $this->company->company_name ?? config('app.name', 'Stafo');
        $this->companyEmail = $this->company->email ?? null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Action Required: Missed Punch-Out for {$this->formattedDate} - {$this->companyName}";

        $branchName = null;
        $departmentName = null;

        try {
            if ($this->employee && method_exists($this->employee, 'branch') && $this->employee->branch) {
                $branchName = $this->employee->branch->name ?? $this->employee->branch->branch_name ?? null;
            }
            if ($this->employee && method_exists($this->employee, 'department') && $this->employee->department) {
                $departmentName = $this->employee->department->name ?? null;
            }
        } catch (\Throwable $e) {}

        $mail = $this->subject($subject);

        // Send from company name & set reply-to company email
        if (!empty($this->companyName)) {
            $fromAddress = config('mail.from.address', 'noreply@stafo.in');
            $mail->from($fromAddress, $this->companyName);
        }

        if (!empty($this->companyEmail) && filter_var($this->companyEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($this->companyEmail, $this->companyName);
        }

        return $mail->view('emails.missed_punchout', [
            'employeeName'   => $this->employee->name ?? 'Employee',
            'employeeId'     => $this->employee->id ?? '',
            'employeeCode'   => $this->employee->emp_id ?? ('#' . ($this->employee->id ?? '')),
            'formattedDate'  => $this->formattedDate,
            'inTime'         => $this->inTime,
            'companyName'    => $this->companyName,
            'companyEmail'   => $this->companyEmail,
            'branchName'     => $branchName,
            'departmentName' => $departmentName,
        ]);
    }
}
