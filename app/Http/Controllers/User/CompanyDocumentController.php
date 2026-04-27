<?php

namespace App\Http\Controllers\user;

use App\Models\DocumentType;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use App\Models\CompanyDocument;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CompanyDocumentController extends Controller
{
    //
    public function index()
    {
        $userId = Auth::id();
        $documents = CompanyDocument::where('company_id', $userId)->orderBy('created_at', 'desc')->get();
        // dd($documents);
        return view('user.company-documents.index', compact('documents'));
    }

    public function create()
    {
        $documentTypes = DocumentType::all(); // Get all document types
        // dd($documentTypes);
        return view('user.company-documents.create', compact('documentTypes'));
    }

    public function edit($id)
    {
        $document = CompanyDocument::findOrFail($id);
        $documentTypes = DocumentType::all();
        return view('user.company-documents.edit', compact('document', 'documentTypes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document' => 'required|file|mimes:pdf,jpg,png,docx',
        ]);

        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();

        $filePath = public_path('uploads/company_documents/' . $fileName);

        $file->move(public_path('uploads/company_documents'), $fileName);
        $userId = Auth::id();
        CompanyDocument::create([
            'company_id' => $userId,
            'document_type_id' => $request->document_type_id,
            'document' =>  $fileName
        ]);

        return redirect()->route('company-documents.index')
            ->with('success', 'Document uploaded successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document' => 'nullable|file|mimes:pdf,jpg,png,docx',
        ]);
        $document = CompanyDocument::findOrFail($id);
        if ($request->hasFile('document')) {
            $oldDocumentPath = public_path('uploads/company_documents/' . $document->document);
            if (file_exists($oldDocumentPath)) {
                unlink($oldDocumentPath);
            }
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/company_documents'), $fileName);

            // Update the document path in the database
            $document->document = $fileName;
        }
        $document->document_type_id = $request->document_type_id;

        $document->save();

        return redirect()->route('company-documents.index')
            ->with('success', 'Document updated successfully!');
    }


    public function destroy($id)
    {
        $document = CompanyDocument::findOrFail($id);

        // dd($document);
        $document->delete();

        return redirect()->route('company-documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    public function documentVerification()
    {
        $id = Auth::id();
        $company = CompanyDetail::find($id);
        return view('user.company.document_verification', compact('company'));
    }

    public function companyDataUpdate(Request $request)
    {
        $type = $request->type;
        $data = $request->data;
        $id = Auth::id();
        if ($type == 'pan') {
            $type = 'pan_number';
        }
        if ($type == 'gstin') {
            $type = 'gst_number';
        }

        $company = CompanyDetail::find($id);
        $company->{$type} = $data;
        $company->save();
        return response()->json(['status' => 'success', 'message' => 'Data added successfully']);
    }
}