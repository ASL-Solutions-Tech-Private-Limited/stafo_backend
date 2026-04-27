<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Models\EmployeeDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class EmployeeDocumentController extends Controller
{

    public function index(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $documents = EmployeeDocument::with('documentType')
            ->where('employee_id', $employeeId)
            ->get();
        $documents->each(function ($document) {
            $document->file_path = asset('uploads/employee_documents/' . $document->file_path);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Documents fetched successfully',
            'data' => $documents
        ], 200);
    }





    public function store(Request $request)
    {
        try {
            // Check if the 'documents' array is present
            if (!$request->has('documents') || !is_array($request->documents)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Documents array is missing or not an array.',
                ], 200);
            }

            // Check if employee_id is provided in the request
            if (!$request->has('employee_id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee ID is required.',
                ], 200);
            }

            // Check if the provided employee_id exists in the employees table
            $employee = Employee::find($request->employee_id);

            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found. Please check the provided Employee ID.',
                ], 200);
            }

            $uploadedDocuments = [];

            // Loop through each document in the 'documents' array
            foreach ($request->documents as $doc) {
                // Check if the document type ID exists in the 'document_types' table
                $documentType = DocumentType::find($doc['document_type_id']);

                if (!$documentType) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid document type ID. Please provide a valid document type.',
                    ], 200);
                }

                // Check if file is uploaded and valid
                if (!isset($doc['file']) || !$doc['file']->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'One or more documents have invalid or missing files.',
                    ], 200);
                }

                // Handle the file upload
                $file = $doc['file'];
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/employee_documents'), $filename);

                // Create the document record in the database
                $document = EmployeeDocument::create([
                    'employee_id' => $request->employee_id,
                    'document_type_id' => $doc['document_type_id'],
                    'document_name' => $doc['document_name'],
                    'file_path' => $filename,
                ]);

                // Generate the file URL
                $fileUrl = asset('uploads/employee_documents/' . $filename);

                // Add the document and its URL to the response array
                $uploadedDocuments[] = [
                    'document' => $document,
                    'file_url' => $fileUrl,
                ];
            }

            // Return the documents with their URLs
            return response()->json([
                'status' => true,
                'message' => 'Documents uploaded successfully.',
                'documents' => $uploadedDocuments,
            ], 201);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while uploading the documents. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }









    // Show an individual employee document
    public function show($id)
    {
        $document = EmployeeDocument::with('documentType')->find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        return response()->json($document, 200);
    }

    // Update an existing employee document

    public function update(Request $request, $id)
    {
        $document = EmployeeDocument::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // Optional file upload
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            // Check if a new file is uploaded
            if ($request->hasFile('file')) {
                // Handle the file upload
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();

                // Move the uploaded file to the 'documents' directory inside the public directory
                $file->move(public_path('uploads/employee_documents'), $filename);

                // Update the file path in the document record
                $document->file_path = $filename;
            }

            // Update other document details
            $document->document_type_id = $request->document_type_id;
            $document->document_name = $request->document_name;

            // Save the updated document record
            $document->save();

            // Generate the URL to access the uploaded file (if it exists)
            $fileUrl = asset('uploads/employee_documents/' . $document->file_path);

            // Return the updated document along with the file URL
            return response()->json([
                'document' => $document,
                'file_url' => $fileUrl,  // Include the file URL in the response
            ], 200);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete an employee document
    public function destroy($id)
    {
        // Find the document by ID
        $document = EmployeeDocument::find($id);

        // If the document doesn't exist, return a 404 response with status false
        if (!$document) {
            return response()->json([
                'status' => false,
                'message' => 'Document not found'
            ], 404); // 404 Not Found
        }

        // Delete the document
        $document->delete();

        // Return a success message with status true
        return response()->json([
            'status' => true,
            'message' => 'Document deleted successfully'
        ], 200); // 200 OK
    }
}