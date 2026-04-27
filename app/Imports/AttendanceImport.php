<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\CompanyDetail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;

class AttendanceImport
{
    private $failureOccurred = false;

    public function import($filePath)
    {
        try {
            //Log::info("Starting import for file: " . $filePath);

            // Load the spreadsheet file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true); // Read all rows


            // Skip the first row (header row)
            $header = array_shift($rows);
            //Log::info("Header row: " . json_encode($header));

            foreach ($rows as $rowIndex => $row) {
                //Log::info("Processing row #$rowIndex: " . json_encode($row));

                // Validate and retrieve foreign keys
                $employee = Employee::where('emp_id', $row['A'])->first();
                $company = CompanyDetail::where('company_name', $row['B'])->first();
                $branch = Branch::where('branch_name', $row['C'])
                    ->where('company_id', $company->id ?? null)
                    ->first();

                // Validation checks and error handling
                // if (!$employee || !$company || !$branch) {
                //     $this->failureOccurred = true;
                //     continue;
                // }
                
                // Process the date
                $dateValue = $row['F']; // Excel date value from column F
                
                // if (is_numeric($dateValue)) {
                //     $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                // } else {
                //     $date = \Carbon\Carbon::createFromFormat('m/d/Y', $dateValue);
                // }
                
                // Try to insert the attendance record, catch any errors during insert
                try {
                    
                    Attendance::create([
                        'company_id' => $company->id,
                        'branch_id' => $branch->id,
                        'employee_id' => $employee->id,
                        'attendance' => $row['E'],
                        'halfday' => strtoupper($row['I']) === 'YES',
                        'date' => $dateValue,
                        'in_time' => $row['G'],
                        'out_time' => $row['H'],
                    ]);
                    
                    Log::info("Row #$rowIndex inserted successfully.");
                } catch (\Exception $e) {
                     
                    //Log::info("Data not inserted.");
                }
            }

            //Log::info("Import completed.");

        } catch (\Exception $e) {
        }
    }

    // Check if any failure occurred
    public function failureOccurred()
    {
        return $this->failureOccurred;
    }
}
