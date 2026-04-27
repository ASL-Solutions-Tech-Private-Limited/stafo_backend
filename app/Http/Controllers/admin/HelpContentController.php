<?php

namespace App\Http\Controllers\admin;

use App\Models\HelpContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class HelpContentController extends Controller
{

    public function index()
    {
        $helpContents = HelpContent::paginate(10);

        return view('admin.help.index', compact('helpContents'));
    }

    // Store the help content in the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:note,video',
            'description' => 'required|string',
            'url' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp4',
        ]);
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
        ];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filehelp = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/help_contents'), $filehelp);
            $data['file_path'] =  $filehelp;
        }

        if ($request->type == 'video' && $request->has('url')) {
            $data['url'] = $request->url;
        }

        HelpContent::create($data);
        Alert::success('Success', 'help contents been saved successfully.');
        return redirect()->back();
    }


    public function edit(HelpContent $helpContent)
    {
        return view('admin.help.edit', compact('helpContent'));
    }
    // Update an existing help content
    public function update(Request $request, HelpContent $helpContent)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:note,video',
            'description' => 'required|string',
            'url' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp4',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'url' => $request->url,
        ];
        if ($request->type == 'note' && $request->hasFile('file')) {
            if ($helpContent->file_path && file_exists(public_path('uploads/help_contents/' . $helpContent->file_path))) {
                unlink(public_path('uploads/help_contents/' . $helpContent->file_path));
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/help_contents'), $fileName);
            $data['file_path'] = $fileName;
        }

        $helpContent->update($data);
        Alert::success('Success', 'help contents been updated successfully.');
        return redirect()->route('admin.help.index');
    }


    // Delete a help content
    public function destroy(HelpContent $helpContent)
    {
        $helpContent->delete();
        Alert::success('Success', 'help contents deleted successfully.');
        return redirect()->back();
    }
}
