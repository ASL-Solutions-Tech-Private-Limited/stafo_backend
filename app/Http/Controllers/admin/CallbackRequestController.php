<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\CallbackRequest;
use App\Http\Controllers\Controller;

class CallbackRequestController extends Controller
{


    public function index(Request $request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $submissions = CallbackRequest::when($name, function ($query, $name) {
            return $query->where('name', 'like', "%{$name}%");
        })
            ->when($phone, function ($query, $phone) {
                return $query->where('phone', 'like', "%{$phone}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('admin.callback.index', compact('submissions'));
    }

    public function destroy($id)
    {
        $submission = CallbackRequest::findOrFail($id);
        $submission->delete();

        return redirect()->route('request-callback')->with('success', 'Callback request deleted successfully.');
    }
}