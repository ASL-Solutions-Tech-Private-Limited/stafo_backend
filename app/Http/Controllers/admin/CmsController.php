<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class CmsController extends Controller
{
    use FileUpload;

    public function index()
    {
        $data = Cms::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.cms.index', compact('data'));
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            // 'short_description' => 'required|max:500',
            // 'long_description' => 'required',
            // 'meta_title' => 'required|max:255',
            // 'meta_description' => 'required|max:500',
            //'image' => 'nullable|image|max:2048',
        ]);
        $imagePath = '';
        if ($request->hasFile('image')) {
            $path = 'uploads/cms';
            $image = $request->file('image');
            $imagePath = FileUpload::imageUpload($image, $path);

            // dd($imagePath);
        }

        $cms = Cms::create([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'image' => $imagePath
        ]);

        Alert::success('Success', 'Cms been saved successfully.');
        return redirect()->route('cms.list')->with('success', 'Cms created successfully.');
    }

    public function edit($id)
    {
        $cms = Cms::where('id', $id)->first();
        return view('admin.cms.edit', compact('cms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            // 'short_description' => 'required|max:500',
            // 'long_description' => 'required',
            // 'meta_title' => 'required|max:255',
            // 'meta_description' => 'required|max:500',
            //'image' => 'nullable|image|max:2048',
        ]);
        $imageName = '';
        $cms = Cms::where('id', $id)->first();
        $imagePath = $cms->image;
        if ($request->hasFile('image')) {
            $path = 'uploads/cms';
            $image = $request->file('image');
            $imageName = FileUpload::imageUpload($image, $path);
        }

        $cms = Cms::where('id', $id)->update([
            'image' => isset($imageName) ? $imageName : $imageName,
            'title' => $request->title,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description
        ]);
        Alert::success('Success', 'Cms has been updated successfully.');
        return redirect()->route('cms.list')->with('success', 'Cms updated successfully.');
    }

    // public function destroy($id)
    // {
    //     $cms = Cms::findOrFail($id);
    //     if ($cms->image) {
    //         Storage::disk('public')->delete($cms->image);
    //     }

    //     $cms->delete();
    //     Alert::success('Success', 'Cms been deleted successfully.');
    //     return redirect()->route('cms.list')->with('success', 'Cms deleted successfully.');
    // }

    public function destroy($id)
    {
        $cms = Cms::findOrFail($id);
        $path = 'uploads/cms/';
        if ($cms->image) {
            $imagePath = public_path($path . $cms->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $cms->delete();
        Alert::success('Success', 'Cms been deleted successfully.');
        return redirect()->route('cms.list')->with('success', 'Cms deleted successfully.');
    }
}