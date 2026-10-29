<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeePunch;
use Illuminate\Support\Facades\Log;

class MarkAbsentEmployees extends Command
{
    // The name and signature of the console command.
    protected $signature = 'employee:mark-absent';

    // The console command description.
    protected $description = 'Mark employees as absent if they haven\'t punched in today';

    // Execute the console command.
    public function handle()
    {
        try {
            // Get the current date
            $yesterday = date('Y-m-d',strtotime("-1 days"));//now()->toDateString();

            $markedCount = 0;

            // Process employees in chunks of 200 to keep memory low and batch query punches/attendance
            Employee::select('id', 'company_id', 'branch_id')
                ->where('status', '1')
                ->chunk(200, function ($employees) use ($yesterday, &$markedCount) {
                    $employeeIds = $employees->pluck('id')->toArray();

                    // Preload punch-ins and existing attendances for the entire chunk in 2 queries
                    $punchedEmployeeIds = EmployeePunch::whereIn('employee_id', $employeeIds)
                        ->whereDate('punch_in', $yesterday)
                        ->pluck('employee_id')
                        ->flip()
                        ->toArray();

                    $existingAttendanceIds = Attendance::whereIn('employee_id', $employeeIds)
                        ->whereDate('date', $yesterday)
                        ->pluck('employee_id')
                        ->flip()
                        ->toArray();

                    foreach ($employees as $employee) {
                        // If no punch and no existing attendance record, mark absent
                        if (!isset($punchedEmployeeIds[$employee->id]) && !isset($existingAttendanceIds[$employee->id])) {
                            Attendance::create([
                                'company_id' => $employee->company_id,
                                'branch_id' => $employee->branch_id,
                                'employee_id' => $employee->id,
                                'attendance' => 'absent',
                                'date' => $yesterday,
                                'in_time' => null,
                                'out_time' => null,
                            ]);
                            $markedCount++;
                        }
                    }
                });

            Log::info("Total {$markedCount} employees marked as absent for {$yesterday}");
            $this->info("Absent employees have been successfully marked. Total marked: {$markedCount}");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error marking absent employees: ' . $e->getMessage());
        }
    }
}