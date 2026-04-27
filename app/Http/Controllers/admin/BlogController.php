<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogComment;
use RealRashid\SweetAlert\Facades\Alert;

class BlogController extends Controller
{
    // Display a listing of the blogs
    public function index()
    {
        $blogs = Blog::paginate(10);
        return view('admin.blog.index', compact('blogs'));
    }

    // Show the form for creating a new blog
    public function create()
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return view('admin.blog.create', compact('categories','tags'));
    }

    // Store a newly created blog in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'auther' => 'required|string|max:100',
            'date' => 'required|date',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = new Blog;
        $data->category_id = $request->category_id;
        $data->title = $request->title;
        // Generate unique slug
        $slug = \Str::slug($validated['title']);
        $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
        $data->slug = $count ? "{$slug}-{$count}" : $slug;
        $data->auther = $request->auther;
        $data->date = $request->date;
        $data->short_description = $request->short_description;
        $data->description = $request->description;
        $data->status = $request->status;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'blog_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/blog'), $imageName);
            $data->image = $imageName;
        }
        $data->save();
        // Attach tags if any
        if ($request->has('tags')) {
            $data->tags()->attach($request->tags);
        }
        return redirect()->route('admin.blog.index')->with('success', 'Blog created successfully.');
    }   

    // Display the specified blog
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return response()->json($blog);
    }

    // Show the form for editing the specified blog
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return view('admin.blog.edit', compact('blog','categories','tags'));
    }

    // Update the specified blog in storage
    public function update(Request $request, $id)
    {     

        $validated = $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'auther' => 'required|string|max:100',
            'date' => 'required|date',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = Blog::findOrFail($id);
        $data->category_id = $request->category_id;
        $data->title = $request->title;
        $slug = \Str::slug($validated['title']);
        $count = Blog::where('slug', 'LIKE', "{$slug}%")->count();
        $data->slug = $count ? "{$slug}-{$count}" : $slug;
        $data->auther = $request->auther;
        $data->date = $request->date;
        $data->short_description = $request->short_description;
        $data->description = $request->description;
        $data->status = $request->status;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'blog_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/blog'), $imageName);
            $data->image = $imageName;
        }
        $data->save();
        // Attach tags if any
        if ($request->has('tags')) {
            $data->tags()->attach($request->tags);
        }
        Alert::success('Success', 'Blog updated successfully.');
        return redirect()->route('admin.blog.index')->with('success', 'Blog updated successfully.');
    }

    // Remove the specified blog from storage
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        Alert::success('Success', 'Blog deleted successfully.');
        return redirect()->route('admin.blog.index')->with('success', 'Blog deleted successfully.');
    }

    public function blogcomments($id)
    {
        $blog = Blog::findOrFail($id);
        $comments = $blog->comments()->paginate(10);
        return view('admin.blog.comments', compact('blog', 'comments'));
    }
    public function blogcommentdelete($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->delete();
        Alert::success('Success', 'Comment deleted successfully.');
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}