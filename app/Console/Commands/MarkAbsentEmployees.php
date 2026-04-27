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

            // Fetch all employees
            $employees = Employee::all();

            // Loop through all employees and mark as absent if no punch-in today
            foreach ($employees as $employee) {
                $employeeInfo = Employee::find($employee->id);
                if($employeeInfo){
                    $employeeInfo->geo_status = 0;
                    $employeeInfo->save();
                }
                
                $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                    ->whereDate('punch_in', $yesterday)
                    ->first();

                if (!$existingPunch) {
                    // Create attendance record for the absent employee
                    $attendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $yesterday)
                        ->first();

                    if (!$attendance) {
                        // Create a new attendance record for the absent employee
                        Attendance::create([
                            'company_id' => $employee->company_id,
                            'branch_id' => $employee->branch_id,
                            'employee_id' => $employee->id,
                            'attendance' => 'absent', // Mark as 'Absent'
                            'date' => $yesterday,
                            'in_time' => null,
                            'out_time' => null,
                        ]);
                    }

                    Log::info("Employee ID {$employee->id} marked as absent for {$yesterday}");
                }
            }

            $this->info('Absent employees have been successfully marked.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error marking absent employees: ' . $e->getMessage());
        }
    }
}