<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comments;

class CommentController extends Controller
{
    public function postLikeOrDislike(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'status'  => 'required|in:0,1'
        ]);

        $userId = $request->user()->id;
        $comment = Comments::where('user_id', $userId)
            ->where('post_id', $request->post_id)
            ->first();

        if ($comment) {
            $comment->update(['comment' => $request->status]);
            $message = $request->status == 0 ? 'Like updated' : 'Dislike updated';
        } else {
            Comments::create([
                'user_id' => $userId,
                'post_id' => $request->post_id,
                'comment' => $request->status
            ]);
            $message = $request->status == 0 ? 'Post liked' : 'Post disliked';
        }

        return response()->json([
            'status' => 200,
            'message' => $message
        ]);
    }
}
