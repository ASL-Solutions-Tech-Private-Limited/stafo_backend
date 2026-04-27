<?php

namespace App\Http\Controllers\User;

use App\Models\Employee;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Models\EmployeeDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{

    // public function index($employeeId)
    // {
    //     // Get employee with documents
    //     $employee = Employee::with('documents.documentType')->findOrFail($employeeId);

    //     // Return the view with employee documents
    //     return view('user.employee.documents.index', compact('employee'));
    // }

    public function create($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $documentTypes = DocumentType::all(); // Get all document types

        return view('user.employee.documents.create', compact('employee', 'documentTypes'));
    }




    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            // 'document_name' => 'required|string',
            'file' => 'required|file|mimes:pdf,docx,jpeg,png',
        ]);

        $employee = Employee::findOrFail($employeeId);

        $file = $request->file('file');

        // Create a unique name for the file to avoid overwriting
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Define the path where the file will be stored (public/employee_documents)
        $filePath = public_path('uploads/employee_documents/' . $fileName);

        // Move the file to the public directory
        $file->move(public_path('uploads/employee_documents'), $fileName);

        // Create new document entry in the database
        EmployeeDocument::create([
            'document_type_id' => $request->document_type_id,
            // 'document_name' => $request->document_name,
            'file_path' =>   $fileName,  // Store just the path relative to public folder
            'employee_id' => $employee->id,
        ]);

        // Redirect with success message
        return redirect()->route('employee.index', $employeeId)->with('success', 'Document uploaded successfully!');
    }


    public function edit($employeeId, $documentId)
    {
        $employee = Employee::findOrFail($employeeId);
        $document = EmployeeDocument::findOrFail($documentId);

        // dd($document);
        $documentTypes = DocumentType::all(); // Get all document types

        return view('user.employee.documents.edit', compact('employee', 'document', 'documentTypes'));
    }

    public function update(Request $request, $employeeId, $documentId)
    {
        $request->validate([
            // 'document_name' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240', // Validate the file if present
        ]);

        // Find the document to update
        $document = EmployeeDocument::findOrFail($documentId);
        // $document->document_name = $request->document_name;
        $document->document_type_id = $request->document_type_id;

        // If a new file is uploaded, handle it
        if ($request->hasFile('file')) {
            // Delete the old file from the public directory (optional, depends on your requirements)
            if ($document->file_path) {
                $oldFilePath = public_path('uploads/employee_documents/' . $document->file_path);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);  // Delete the old file
                }
            }

            // Get the new uploaded file
            $file = $request->file('file');

            // Create a unique name for the new file to avoid overwriting
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Move the file to the public/employee_documents directory
            $file->move(public_path('uploads/employee_documents'), $fileName);

            // Store the new file name in the database
            $document->file_path = $fileName;
        }

        // Save the updated document
        $document->save();

        return redirect()->route('employee.index', $employeeId)->with('success', 'Document updated successfully!');
    }



    public function destroy($documentId)
    {
        $document = EmployeeDocument::findOrFail($documentId);

        // Delete the document file
        Storage::delete($document->file_path);

        // Delete the document record
        $document->delete();

        return back()->with('success', 'Document deleted successfully!');
    }
}