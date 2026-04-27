<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\CompanyDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Correct import for Validator

class DocumentController extends Controller
{


    public function uploadDocument(Request $request)
    {


        try {
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $company_id = Auth::id();

                foreach ($request->file('document') as $key => $file) {
                    $filename = $request->document_type_id[$key] . '_' . $company_id . '_' . time() . $key . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/documents'), $filename);

                    $companyDocument = CompanyDocument::create([
                        'company_id' => $company_id,
                        'document_type_id' => $request->document_type_id[$key],
                        'document' => $filename,
                    ]);
                }


                return response()->json([
                    'status' => true,
                    'message' => 'Document uploaded successfully',
                    'data' => [
                        'company_document' => $companyDocument,
                        'document_url' => asset('uploads/documents/' . $filename),
                    ]
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'No document was uploaded.',
            ], 200);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while uploading the document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDocument(Request $request, $document_id)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:document_types,id',
            'document' => 'nullable|mimes:pdf,jpeg,png,jpg,doc,docx,txt|max:2048',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            // Find the existing document
            $companyDocument = CompanyDocument::find($document_id);

            if (!$companyDocument) {
                return response()->json([
                    'status' => false,
                    'message' => 'Document not found',
                ], 200);
            }

            // Get the company_id from the authenticated user (Auth::id())
            $company_id = Auth::id();

            // If a new document is provided, handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '.' . $file->getClientOriginalExtension();

                // Delete old document file
                if (file_exists(public_path('uploads/documents/' . $companyDocument->document))) {
                    unlink(public_path('uploads/documents/' . $companyDocument->document));
                }

                // Save the new document in the 'documents' directory
                $file->move(public_path('uploads/documents'), $filename);

                // Update the document record
                $companyDocument->update([
                    'company_id' => $company_id,
                    'document_type_id' => $request->document_type_id,
                    'document' => $filename, // Update with new filename
                ]);
            } else {
                // If no new document is provided, update only the document type ID
                $companyDocument->update([
                    'document_type_id' => $request->document_type_id,
                ]);
            }

            // Return the response with updated document details
            return response()->json([
                'status' => true,
                'message' => 'Document updated successfully',
                'data' => [
                    'company_document' => $companyDocument,
                    'document_url' => asset('uploads/documents/' . $companyDocument->document), // URL for the updated document
                ]
            ], 200);
        } catch (\Exception $e) {
            // Catch any unexpected errors and return an error message
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getDocumentsByCompany($company_id)
    {
        try {
            $company = CompanyDetail::find($company_id);

            if (!$company) {
                return response()->json([
                    'status' => false,
                    'message' => 'Company not found',
                ], 200);
            }

            // Fetch documents related to the given company_id
            $documents = CompanyDocument::where('company_id', $company_id)->get();

            if ($documents->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No documents found for this company',
                ], 200);
            }

            // Return the documents along with their URLs and document type name
            return response()->json([
                'status' => true,
                'message' => 'Documents fetched successfully',
                'data' => $documents->map(function ($document) {
                    // Use the documentType() relationship to get the name of the document type
                    $documentType = $document->documentType ? $document->documentType->document_name : '';

                    return [
                        'document_id' => $document->id,
                        'company_id' => $document->company_id,
                        'document_type_id' => $document->document_type_id,
                        'document_type_name' => $documentType, // Add the document type name
                        'document' => $document->document,
                        'document_url' => asset('uploads/documents/' . $document->document), // Generate URL for each document
                    ];
                }),
            ], 200);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the documents',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}