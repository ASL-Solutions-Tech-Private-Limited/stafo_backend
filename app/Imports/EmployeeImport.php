<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\CompanyDetail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EmployeeImport
{
    private $failureOccurred = false;

    public function import($filePath, $companyId = null)
    {
        $companyId = $companyId ?: Auth::id();
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
                
                // Try to insert the attendance record, catch any errors during insert
                try {
                   
                    $checkphone = Employee::where('phone', $row['C'])->first();
                    if ($checkphone) {
                        //Log::info("Phone already exists: " . $row['C']);
                        continue; // Skip this row if phone already exists
                    }

                    $checkemail = Employee::where('email', $row['B'])->first();
                    if ($checkemail) {
                        //Log::info("Email already exists: " . $row['B']);
                        continue; // Skip this row if email already exists
                    }
                    Employee::create([
                        'company_id' => $companyId,
                        'name' => $row['A'], // Assuming 'A' is the name column
                        'email' => $row['B'],
                        'phone' => $row['C'],
                        'position' => $row['D'],
                        'salary' => $row['E'],
                        'date_of_birth' => $row['F'],
                        'gender' => $row['G'],
                        'marital_status' => $row['H'],
                        'blood_group' => $row['I'],
                        'address' => $row['J'],
                        'pin' => $row['K'],
                        'date_of_joining' => $row['L'],
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
