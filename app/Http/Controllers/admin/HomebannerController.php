<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AppBanner;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class HomebannerController extends Controller
{
    use FileUpload;

    
    public function index()
    {
        $data = AppBanner::where('type','2')->latest()->paginate(10);
        return view('admin.homebanner.index', compact('data'));
    }

    public function create()
    {
        return view('admin.homebanner.create');
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'title' => 'nullable|max:255',
                'image' => 'required|image|max:2048',
            ]);
            $imagePath = '';
            if ($request->hasFile('image')) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $imageName = 'homebanner_' . time() . '.' . $extension;
                $request->file('image')->move(public_path('uploads/homebanner'), $imageName);
                $data = AppBanner::create([
                    'title' => $request->title,
                    'image' => $imageName,
                    'type' => '2',
                ]);
                Alert::success('Success', 'Record created successfully.');
                return redirect()->route('homebanner.index')->with('success', 'Record created successfully.');
            } else {
                Alert::error('Error', 'Image is required.');
                return redirect()->route('homebanner.index')->with('error', 'Image is required.');
            }
        }catch(\Exception $e){
            Alert::error('Error', $e->getMessage());
            return redirect()->route('homebanner.index')->with('error', $e->getMessage());
        }        
    }

    public function edit($id)
    {
        $appbanner = AppBanner::where('id', $id)->first();
        return view('admin.homebanner.edit', compact('appbanner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|max:255',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $appbanner = AppBanner::where('id', $id)->first();
        $imageName = $appbanner->image;
        $imagePath = $appbanner->image;
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'homebanner_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/homebanner'), $imageName);
        }

        $appbanner = AppBanner::where('id', $id)->update([
            'image' => isset($imageName) ? $imageName : $imageName,
            'title' => $request->title,
        ]);
        Alert::success('Success', 'Record updated successfully.');
        return redirect()->route('homebanner.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        $appbanner = AppBanner::findOrFail($id);
        if ($appbanner->image) {
        
            @unlink(public_path('uploads/homebanner/' . $appbanner->image));
        }

        $appbanner->delete();
        Alert::success('Success', 'Record deleted successfully.');
        return redirect()->route('homebanner.index')->with('success', 'Record deleted successfully.');
    }
}