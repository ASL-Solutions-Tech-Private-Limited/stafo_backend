<?php

namespace App\Http\Controllers\admin;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $company_id = $request->company_id;
        $records = Ticket::when($company_id, function ($query, $company_id) {
            return $query->where('company_id', $company_id);
        })->orderBy('id', 'desc')
            ->paginate(10);
        return view('admin.tickets.list', compact('records'));
    }

    // public function reply(Request $request, $id)
    // {
    //     $feedback = Ticket::findOrFail($id);
    //     $feedback->reply = $request->input('reply');
    //     $feedback->save();

    //     return redirect()->route('admin.tickets_list')->with('success', 'Reply sent successfully.');
    // }


    public function reply(Request $request, $id)
    {
        // dd($request->all());
        // Find the ticket
        $ticket = Ticket::findOrFail($id);

        $admin = Auth::id();

        // Create a new reply
        $ticket->replies()->create([
            'reply' => $request->input('reply'),
            'message_by' => 'Admin',
            'admin_id' => $admin,
        ]);

        return redirect()->route('admin.tickets_list')->with('success', 'Reply sent successfully.');
    }


    public function destroy($id)
    {
        $feedback = Ticket::findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.tickets_list')->with('success', 'Ticket deleted successfully');
    }
}