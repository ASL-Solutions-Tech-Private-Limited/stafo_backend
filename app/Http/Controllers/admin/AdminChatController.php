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
        $company_id = $company_id;
        $company = CompanyDetail::select('company_name')->where('id', $company_id)->first();
        $company_name = $company->company_name;
        $chats = Chat::where('company_id', $company_id)->get();
        return view('admin.chat.index', compact('chats','company_id','company_name'));
    }
    public function savechat($company_id,Request $request){
        $message = $request->message;

        $chat = new Chat;
        $chat->company_id = $company_id;
        $chat->sender = 1;
        $chat->receiver = $company_id;
        $chat->message = $message;
        $chat->message_by = 'admin'; 
        $chat->save();
    }

    public function chatList(){
        $chats = Chat::groupBy('company_id')->get();
        //dd($chats);
        return view('admin.chat.list', compact('chats'));
    }

    public function getadminChat($company_id)
    {
        $chats = Chat::where('company_id', $company_id)->get();
        // $chatSection = '';
        // if(count($chats) > 0){
        //     foreach($chats as $chat){
        //         $chatSection =  '<div class="message'. $chat->message_by == "admin" ? "user" : "bot" . '">                                 
        //             <p>'. $chat->message .'</p>
        //         </div>';
        //     }
        // }
        return response()->json($chats);
    }

}