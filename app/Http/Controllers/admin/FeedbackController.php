<?php

namespace App\Http\Controllers\admin;

use App\Models\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $company_id = $request->company_id;
        // Query for submissions, applying filters if they exist
        $records = Feedback::when($company_id, function ($query, $company_id) {
            return $query->where('company_id', $company_id);
        })->orderBy('id', 'desc')
            ->paginate(10);
        return view('admin.feedback.list', compact('records'));
    }

    public function reply(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->reply = $request->input('reply');
        $feedback->save();

        return redirect()->route('admin.feedback_list')->with('success', 'Reply sent successfully.');
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.feedback_list')->with('success', 'Feedback deleted successfully');
    }
}