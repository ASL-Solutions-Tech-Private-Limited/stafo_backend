<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogTag;


class BlogTagController extends Controller
{
    // List all blog tags
    public function index()
    {
        $tags = BlogTag::paginate(10);
        return view('admin.blog_tag.index', compact('tags'));
    }

    // Show form to create a new tag
    public function create()
    {
        return view('admin.blog_tag.create');
    }

    // Store a new tag
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name',
        ]);

        BlogTag::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 1, // Assuming status            
        ]);

        return redirect()->route('admin.blog_tag.index')
            ->with('success', 'Tag created successfully.');
    }

    // Show form to edit a tag
    public function edit($id)
    {
        $tag = BlogTag::findOrFail($id);
        return view('admin.blog_tag.edit', compact('tag'));
    }

    // Update a tag
    public function update(Request $request, $id)
    {
        $tag = BlogTag::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name,' . $tag->id,
        ]);

        $tag->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blog_tag.index')
            ->with('success', 'Tag updated successfully.');
    }

    // Delete a tag
    public function destroy($id)
    {
        $tag = BlogTag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.blog_tag.index')
            ->with('success', 'Tag deleted successfully.');
    }
}