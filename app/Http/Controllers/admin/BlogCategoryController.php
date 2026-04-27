<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;


class BlogCategoryController extends Controller
{
    // List all blog categories
    public function index()
    {
        $categories = BlogCategory::paginate(10);
        return view('admin.blog_categories.index', compact('categories'));
    }

    // Show form to create a new category
    public function create()
    {
        return view('admin.blog_categories.create');
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 1, // Assuming status            
        ]);

        return redirect()->route('admin.blog_categories.index')
            ->with('success', 'Category created successfully.');
    }

    // Show form to edit a category
    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        return view('admin.blog_categories.edit', compact('category'));
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blog_categories.index')
            ->with('success', 'Category updated successfully.');
    }

    // Delete a category
    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.blog_categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}