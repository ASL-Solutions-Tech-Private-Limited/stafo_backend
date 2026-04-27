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
        $chats = Chat::where('company_id', $company_id)->get();
        return view('user.chat.index', compact('chats','company_id'));
    }
    public function savechat(Request $request){
        $userId = Auth::id();
        $message = $request->message;

        $chat = new Chat;
        $chat->company_id = $userId;
        $chat->sender = $userId;
        $chat->receiver = 1;
        $chat->message = $message;
        $chat->message_by = 'company'; 
        $chat->save();
    }

    public function getChat()
    {
        $company_id = Auth::id();
        $chats = Chat::where('company_id', $company_id)->get();
        return response()->json($chats);
    }

}