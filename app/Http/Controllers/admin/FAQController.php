<?php

namespace App\Http\Controllers\admin;

use App\Models\FAQ;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class FAQController extends Controller
{
    use FileUpload;

    public function index()
    {
        $faqs = FAQ::orderBy('id', 'asc')
            ->paginate(10);
        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = 'uploads/faq';
            $image = $request->file('image');
            $imagePath = FileUpload::imageUpload($image, $path);
        }

        FAQ::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'image' => $imagePath,
        ]);

        Alert::success('Success', 'FAQ has been saved successfully.');
        return redirect()->route('faq.index')->with('success', 'FAQ created successfully.');
    }

    public function edit($id)
    {
        $faq = FAQ::findOrFail($id);
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $faq = FAQ::findOrFail($id);
        $imageName = $faq->image;

        if ($request->hasFile('image')) {
            $path = 'uploads/faq';
            $image = $request->file('image');
            $imageName = FileUpload::imageUpload($image, $path);

            // Delete old file if exists
            if ($faq->image && file_exists(public_path('uploads/faq/' . $faq->image))) {
                @unlink(public_path('uploads/faq/' . $faq->image));
            }
        }

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'image' => $imageName,
        ]);

        Alert::success('Success', 'FAQ has been updated successfully.');
        return redirect()->route('faq.index')->with('success', 'FAQ updated successfully.');
    }

    // Delete an FAQ
    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);

        if ($faq->image && file_exists(public_path('uploads/faq/' . $faq->image))) {
            @unlink(public_path('uploads/faq/' . $faq->image));
        }

        $faq->delete();
        Alert::success('Success', 'FAQ has been deleted successfully.');
        return redirect()->route('faq.index')->with('success', 'FAQ deleted successfully.');
    }
}