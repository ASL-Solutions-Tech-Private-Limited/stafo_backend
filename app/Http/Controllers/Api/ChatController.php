<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChatController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/chat/send",
     *     summary="Send a chat message",
     *     tags={"Chat"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id","message"},
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Hello support, need assistance.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Chat send successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/chat/get",
     *     summary="Get chat history for company",
     *     tags={"Chat"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id"},
     *             @OA\Property(property="company_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chats retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Chats retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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