<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChatController extends Controller
{
    public function sendchat(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $message = $request->message;

            $chat = new Chat;
            $chat->company_id = $company_id;
            $chat->sender = $company_id;
            $chat->receiver = 1;
            $chat->message = $message;
            $chat->message_by = 'company'; 
            $chat->save();

            //$chats = Chat::where('company_id', $company_id)->get();
            return response()->json([
                'success' => true,
                'message' => 'Chat send successfully.',
                'data' => $chat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching countries.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChat(Request $request)
    {
        try {
            $company_id = $request->company_id;
            $chats = Chat::where('company_id', $company_id)->get(); 
            return response()->json([
                'success' => true,
                'message' => 'Chats retrieved successfully.',
                'data' => $chats,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching states.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}