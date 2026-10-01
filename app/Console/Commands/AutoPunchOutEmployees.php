<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeePunch;
use App\Models\Shift;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoPunchOutEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:auto-punchout {--date= : The date to process auto punch-out for (format Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically process open punches for employees who forgot to punch-out today (keeping punch-out and out_time as null and emailing employees from company)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $targetDate = $this->option('date') ?: date('Y-m-d');

            $this->info("Running auto punch-out (setting/keeping null) for date: {$targetDate}");

            $openPunches = EmployeePunch::whereDate('punch_in', $targetDate)
                ->whereNull('punch_out')
                ->get();

            $count = 0;
            $processedEmployeeIds = [];

            foreach ($openPunches as $punch) {
                $employee = Employee::with(['company', 'branch', 'department', 'shift'])->find($punch->employee_id);
                if (!$employee) {
                    continue;
                }

                // Ensure punch_out remains null
                $punch->update([
                    'punch_out' => null,
                ]);

                // Update Attendance record: ensure out_time remains null
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $targetDate)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'out_time' => null,
                    ]);
                }

                // Reset geo_status
                $employee->geo_status = 0;
                $employee->save();

                // Send push notification
                if (!empty($employee->fcm_token)) {
                    $notification_message = "You forgot to punch-out for " . date('d M Y', strtotime($targetDate)) . ". Your punch-out has been recorded.";
                    Helper::sendPushNotification($employee->fcm_token, $notification_message);
                }

                // Send Email notification to employee from company side
                if (!in_array($employee->id, $processedEmployeeIds)) {
                    $inTimeStr = $punch->punch_in 
                        ? Carbon::parse($punch->punch_in)->format('h:i A') 
                        : ($attendance && $attendance->in_time ? Carbon::parse($attendance->in_time)->format('h:i A') : null);

                    Helper::sendMissedPunchOutEmailNotification($employee, $targetDate, $inTimeStr);
                    $processedEmployeeIds[] = $employee->id;
                }

                Log::info("Employee ID {$employee->id} ({$employee->name}) auto punch-out processed (out_time set to null) for {$targetDate}");
                $count++;
            }

            // Also ensure any Attendance records for the day with in_time and missing out_time have out_time as null
            $openAttendances = Attendance::whereDate('date', $targetDate)
                ->whereNotNull('in_time')
                ->whereNull('out_time')
                ->get();

            foreach ($openAttendances as $att) {
                $att->update(['out_time' => null]);

                if (!in_array($att->employee_id, $processedEmployeeIds)) {
                    $emp = Employee::with(['company', 'branch', 'department', 'shift'])->find($att->employee_id);
                    if ($emp) {
                        $inTimeStr = $att->in_time ? Carbon::parse($att->in_time)->format('h:i A') : null;
                        Helper::sendMissedPunchOutEmailNotification($emp, $targetDate, $inTimeStr);
                        $processedEmployeeIds[] = $emp->id;
                    }
                }
            }

            $this->info("Successfully processed {$count} employee(s) (punch-out and out_time kept as null, missed punch-out emails dispatched).");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error in AutoPunchOutEmployees command: ' . $e->getMessage());
        }
    }
}
