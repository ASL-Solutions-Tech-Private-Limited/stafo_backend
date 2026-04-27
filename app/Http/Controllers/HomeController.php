<?php

namespace App\Http\Controllers;

use App\Models\Cms;
use App\Models\FAQ;
use App\Models\Package;
use App\Models\AppBanner;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use App\Models\CallbackRequest;
use App\Models\ContactFormSubmission;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    //

    public function index()
    {
        $homebanner =  AppBanner::where('type', '2')->where('status', '1')->get();
        return view('welcome', compact('homebanner'));
    }

    public function price()
    {
        $packages = Package::with('features')->where('status','active')->get();
        return view('price', compact('packages'));
    }

    public function requestCallback(Request $request)
    {
        // Validate form data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Create a new callback request
        CallbackRequest::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Alert::success('Success', 'Employee Details has been saved successfully.');
        // Return response
        return response()->json([
            'status' => 'success',
            'message' => 'Callback request submitted successfully.'
        ]);
    }


    public function aboutUs()
    {
        return view('about-us');
    }

    public function contactUs()
    {
        return view('conatct');
    }


    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Save the contact form submission
        $submission = ContactFormSubmission::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => 'Your contact form has been submitted successfully!',
        ], 200);
    }

    public function privacy()
    {
        $data = Cms::find(7);

        return view('cms', compact('data'));
    }

    public function terms()
    {
        $data = Cms::find(6);

        // dd($data);

        return view('cms', compact('data'));
    }

    public function carrer()
    {
        $data = Cms::find(8);
        return view('cms', compact('data'));
    }

    public function faq()
    {
        $faq = FAQ::all();
        // dd($faq);
        return view('faq', compact('faq'));
    }

    public function blog()
    {
        $blogs = Blog::with(['category','tags'])->where('status','Published')->paginate(10);
        $recentblogs = Blog::where('status','Published')->orderBy('id','desc')->limit(3)->get();
        $categories = BlogCategory::where('status', '1')->get();
        $tags = BlogTag::where('status', '1')->get();
        return view('blog', compact('blogs','recentblogs','categories', 'tags'));
    }

    public function blogDetails($id,$slug){
        $blog = Blog::with('category', 'tags','comments')->findOrFail($id);
        return view('blog_details', compact('blog'));

    }

    public function blogCommentStore(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => 'required|string',
        ]);

        $blog = Blog::findOrFail($id);
        $blog->comments()->create([
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }
}