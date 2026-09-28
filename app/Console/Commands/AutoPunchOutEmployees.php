<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeePunch;
use App\Models\Shift;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Automatically punch-out employees who forgot to punch-out today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $targetDate = $this->option('date') ?: date('Y-m-d');
            $outTime = '00:00:00';
            $punchOutDateTime = $targetDate . ' ' . $outTime;

            $this->info("Running auto punch-out with 00:00:00 time for date: {$targetDate}");

            $openPunches = EmployeePunch::whereDate('punch_in', $targetDate)
                ->whereNull('punch_out')
                ->get();

            $count = 0;

            foreach ($openPunches as $punch) {
                $employee = Employee::find($punch->employee_id);
                if (!$employee) {
                    continue;
                }

                // Punchout time set as 00:00:00 format
                $punch->update([
                    'punch_out' => $punchOutDateTime,
                ]);

                // Update Attendance record: set out_time as 00:00:00
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $targetDate)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'out_time' => $outTime,
                    ]);
                }

                // Reset geo_status
                $employee->geo_status = 0;
                $employee->save();

                // Send notification
                if (!empty($employee->fcm_token)) {
                    $notification_message = "You forgot to punch-out for " . date('d M Y', strtotime($targetDate)) . ". Your punch-out has been recorded as 00:00:00.";
                    Helper::sendPushNotification($employee->fcm_token, $notification_message);
                }

                Log::info("Employee ID {$employee->id} ({$employee->name}) auto punch-out processed with 00:00:00 out_time for {$targetDate}");
                $count++;
            }

            // Also check any Attendance records for the day where in_time is not null and out_time is null
            $openAttendances = Attendance::whereDate('date', $targetDate)
                ->whereNotNull('in_time')
                ->whereNull('out_time')
                ->get();

            foreach ($openAttendances as $att) {
                $att->update(['out_time' => $outTime]);
            }

            $this->info("Successfully processed {$count} employee(s) with 00:00:00 punch-out.");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error in AutoPunchOutEmployees command: ' . $e->getMessage());
        }
    }
}
