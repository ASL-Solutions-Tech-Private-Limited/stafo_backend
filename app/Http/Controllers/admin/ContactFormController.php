<?php

namespace App\Http\Controllers\admin;

use App\Models\ContactFormSubmission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function index(Request $request)
    {

        // Get the filter inputs
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');

        // Query for submissions, applying filters if they exist
        $submissions = ContactFormSubmission::when($name, function ($query, $name) {
            return $query->where('full_name', 'like', "%{$name}%");
        })
            ->when($email, function ($query, $email) {
                return $query->where('email', 'like', "%{$email}%");
            })
            ->when($phone, function ($query, $phone) {
                return $query->where('phone', 'like', "%{$phone}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.contact.contact_form_submissions', compact('submissions'));
    }

    public function destroy($id)
    {
        // Find the submission by its ID
        $submission = ContactFormSubmission::findOrFail($id);

        // Delete the submission
        $submission->delete();

        // Redirect back with a success message
        return redirect()->route('admin.contact_form_submissions')->with('success', 'Submission deleted successfully.');
    }
}