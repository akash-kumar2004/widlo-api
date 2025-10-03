<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class ChatController extends Controller
{
    public function chat_send(Request $request)
    {
        $student = $request->user();

        $message = Message::create([
            'from_id' => $student->id,
            'to_id' => $request->to_id,
            'message' => $request->message,
            'status' => '0',
            'user_type' => 'user',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message Sent',
            'data' => $message,
        ]);
    }


    public function get_message(Request $request)
    {
        $student = $request->user(); // Authenticated user
        $to_id = $request->query('to_id');
        $from_id = $student->id;

        if (!$to_id) {
            return response()->json(['error' => 'Recipient ID required'], 400);
        }

        $messages = Message::where(function ($query) use ($from_id, $to_id) {
            $query->where('from_id', $from_id)->where('to_id', $to_id);
        })->orWhere(function ($query) use ($from_id, $to_id) {
            $query->where('from_id', $to_id)->where('to_id', $from_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }
}
