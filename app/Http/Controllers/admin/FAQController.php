<?php

namespace App\Http\Controllers\admin;

use App\Models\FAQ;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class FAQController extends Controller
{

    public function index()
    {
        $faqs = FAQ::orderBy('created_at', 'desc')
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
        ]);

        FAQ::create([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        Alert::success('Success', 'faq has been saved successfully.');
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
            'question' => 'nullable|string|max:255',
            'answer' => 'nullable|string',
        ]);

        $faq = FAQ::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        Alert::success('Success', 'faq has been updated successfully.');
        return redirect()->route('faq.index')->with('success', 'FAQ updated successfully.');
    }

    // Delete an FAQ
    public function destroy($id)
    {
        FAQ::findOrFail($id)->delete();
        return redirect()->route('faq.index')->with('success', 'FAQ deleted successfully.');
    }
}