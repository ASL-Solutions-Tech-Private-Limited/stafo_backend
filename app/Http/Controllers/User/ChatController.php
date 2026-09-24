<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\PackageFeature;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    
    public function index()
    {
        $package_id = Auth::user()->package_id;
        $subscription_end = Auth::user()->subscription_end;
        $is_active_plan = true;
        $message = '';

        
        if (empty($package_id)) {
            $is_active_plan = false;
            $message = "You don't have any active plan. Please upgrade your plan to access this feature.";
        }else{
            if ($subscription_end < date('Y-m-d')) {
                $is_active_plan = false;
                $message = "Your current subscription plan has expired. Please upgrade your plan to access this feature.";
            } else {
                $package_feature = PackageFeature::where('package_id', $package_id)->where('features_id', 9)->first();
                if ($package_feature) {
                    if ($package_feature->feature_value == 'No') {
                        $is_active_plan = false;
                        $message = "Your current subscription plan does not have access to this feature. Please upgrade your plan to access this feature.";
                    }
                }
            }
        }
        
        if ($is_active_plan == false) {
            return view('user.restriction_check', compact('message'));
        }
        $company_id = Auth::id();
        $chats = Chat::where('company_id', $company_id)->orderBy('id', 'asc')->get();
        // Mark admin messages as seen by company
        Chat::where('company_id', $company_id)->where('message_by', 'admin')->where(function($q) {
            $q->whereNull('is_seen_user')->orWhere('is_seen_user', 0);
        })->update(['is_seen_user' => 1]);

        $chat_status = Auth::user()->chat_status ?? 'open';
        return view('user.chat.index', compact('chats','company_id', 'chat_status'));
    }

    public function toggleStatus(Request $request)
    {
        $company_id = Auth::id();
        $status = $request->input('status', 'closed');
        $reason = $request->input('reason', '');

        $company = \App\Models\CompanyDetail::where('id', $company_id)->first();
        if ($company) {
            $company->chat_status = $status;
            $company->save();
        }

        $cleanReason = $reason ? " (Reason: {$reason})" : '';
        $actionText = $status === 'closed'
            ? "🔒 Support session was marked as Resolved & Closed by Company.{$cleanReason}"
            : "🔓 Support session was Reopened by Company.";

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = $company_id;
        $chat->receiver = 1;
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

    public function savechat(Request $request){
        $userId = Auth::id();
        $message = trim($request->message);

        if (empty($message)) {
            return response()->json(['status' => 'error', 'message' => 'Message cannot be empty'], 422);
        }

        $chat = new Chat;
        $chat->company_id = $userId;
        $chat->sender = $userId;
        $chat->receiver = 1;
        $chat->message = $message;
        $chat->message_by = 'company';
        $chat->is_seen_user = 1;
        $chat->is_seen_admin = 0;
        $chat->save();

        return response()->json(['status' => 'ok', 'data' => $chat]);
    }

    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg,pdf|max:10240',
            'message' => 'nullable|string'
        ]);

        $company_id = Auth::id();
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/chat_attachments'), $fileName);
        $filePath = 'uploads/chat_attachments/' . $fileName;

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = $company_id;
        $chat->receiver = 1;
        $chat->message = $request->input('message', '') ?? '';
        $chat->attachment = $filePath;
        $chat->message_by = 'company';
        $chat->is_seen_user = 1;
        $chat->is_seen_admin = 0;
        $chat->save();

        return response()->json([
            'status' => 'ok',
            'data' => $chat,
            'file_url' => asset($filePath),
            'file_name' => $originalName
        ]);
    }

    public function getChat()
    {
        $company_id = Auth::id();
        $chats = Chat::where('company_id', $company_id)->orderBy('id', 'asc')->get();
        return response()->json($chats);
    }
}