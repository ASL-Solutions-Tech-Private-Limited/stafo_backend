<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    //

    public function index()
    {
        $documenttype = DocumentType::orderBy('created_at', 'desc')->get();

        return view('admin.documenttypes.list')->with(['documenttype' => $documenttype]);
    }

    public function add()
    {
        return view('admin.documenttypes.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'document_name' => 'required|string|max:255',
        ]);

        //DocumentType::create($validatedData);
        $DocumentType = new DocumentType;
        $DocumentType->document_name = $request->document_name;
        $DocumentType->save();

        return redirect()->route('document_list')->with('success', 'Document type added successfully!');
    }

    public function edit(Request $request, $id)
    {
        $documenttype = DocumentType::find($id);
        return view('admin.documenttypes.edit')->with(['documenttype' => $documenttype]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $DocumentType = DocumentType::find($id);
        $DocumentType->document_name = $request->document_name;
        $DocumentType->save();
        return redirect()->route('document_list')->with('success', 'Document type updated successfully!');
    }

    public function delete(Request $request, $id)
    {
        $documenttype = DocumentType::find($id);
        $documenttype->delete();
        return redirect()->route('document_list')->with('success', 'Document type deleted successfully!');
    }
}