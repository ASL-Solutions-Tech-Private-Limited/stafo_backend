<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    
    public function index($company_id)
    {
        $company = CompanyDetail::where('id', $company_id)->first();
        $company_name = $company ? $company->company_name : 'Company #' . $company_id;
        if ($company && $company->package_id) {
            $company->package = \App\Models\Package::find($company->package_id);
        }
        $chats = Chat::where('company_id', $company_id)->orderBy('id', 'asc')->get();

        // Mark company messages as seen by admin
        Chat::where('company_id', $company_id)->where('message_by', 'company')->where(function($q) {
            $q->whereNull('is_seen_admin')->orWhere('is_seen_admin', 0);
        })->update(['is_seen_admin' => 1]);

        return view('admin.chat.index', compact('chats','company_id','company_name','company'));
    }

    public function savechat($company_id, Request $request){
        $message = trim($request->message);
        if (empty($message)) {
            return response()->json(['status' => 'error', 'message' => 'Message cannot be empty'], 422);
        }

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = 1;
        $chat->receiver = $company_id;
        $chat->message = $message;
        $chat->message_by = 'admin';
        $chat->is_seen_user = 0;
        $chat->is_seen_admin = 1;
        $chat->save();

        return response()->json(['status' => 'ok', 'data' => $chat]);
    }

    public function uploadAttachment($company_id, Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg,pdf|max:10240',
            'message' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/chat_attachments'), $fileName);
        $filePath = 'uploads/chat_attachments/' . $fileName;

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = 1;
        $chat->receiver = $company_id;
        $chat->message = $request->input('message', '') ?? '';
        $chat->attachment = $filePath;
        $chat->message_by = 'admin';
        $chat->is_seen_user = 0;
        $chat->is_seen_admin = 1;
        $chat->save();

        return response()->json([
            'status' => 'ok',
            'data' => $chat,
            'file_url' => asset($filePath),
            'file_name' => $originalName
        ]);
    }

    public function toggleStatus($company_id, Request $request)
    {
        $status = $request->input('status', 'closed');
        $reason = $request->input('reason', '');

        $company = CompanyDetail::where('id', $company_id)->first();
        if ($company) {
            $company->chat_status = $status;
            $company->save();
        }

        $cleanReason = $reason ? " (Reason: {$reason})" : '';
        $actionText = $status === 'closed'
            ? "🔒 Support session was marked as Resolved & Closed by Super Admin.{$cleanReason}"
            : "🔓 Support session was Reopened by Super Admin.";

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = 1;
        $chat->receiver = $company_id;
        $chat->message = $actionText;
        $chat->message_by = 'system';
        $chat->is_seen_user = 1;
        $chat->is_seen_admin = 1;
        $chat->save();

        return response()->json([
            'status' => 'ok',
            'chat_status' => $status,
            'system_message' => $chat
        ]);
    }

    public function chatList(){
        $companyIds = Chat::select('company_id')->distinct()->pluck('company_id');
        $companies = CompanyDetail::whereIn('id', $companyIds)->get();

        foreach ($companies as $comp) {
            $latestChat = Chat::where('company_id', $comp->id)->orderBy('id', 'desc')->first();
            $comp->latest_message = $latestChat ? $latestChat->message : '';
            $comp->latest_time = $latestChat ? $latestChat->created_at : null;
            $comp->latest_by = $latestChat ? $latestChat->message_by : null;
            $comp->unread_count = Chat::where('company_id', $comp->id)
                ->where('message_by', 'company')
                ->where(function($q) {
                    $q->whereNull('is_seen_admin')->orWhere('is_seen_admin', 0);
                })
                ->count();
        }

        $companies = $companies->sortByDesc(function ($comp) {
            return $comp->latest_time ? $comp->latest_time->timestamp : 0;
        })->values();

        return view('admin.chat.list', compact('companies'));
    }

    public function getadminChat($company_id)
    {
        $chats = Chat::where('company_id', $company_id)->orderBy('id', 'asc')->get();
        return response()->json($chats);
    }
}