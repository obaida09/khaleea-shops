<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEnd\StoreCommentRequest;
use App\Http\Resources\FrontEnd\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewReplyNotification;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    // Show all comments for a specific post
    public function index($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->whereParentId(null)->latest()->get();

        return CommentResource::collection($comments->load('replies'));
    }

    // Store a new comment for a post
    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        $comment = Comment::create($data);

        if ($comment->parent_id == null) {
            // Notify the post owner
            $post = Post::find($data['post_id']);
            if ($post->user_id != $data['user_id']) {
                $post->user->notify(new NewCommentNotification($comment));
            }
        } else {
            // Notify the original comment owner
            $findComment = Comment::find($data['parent_id']);
            if ($findComment->user_id != $data['user_id']) {
                $findComment->user->notify(new NewReplyNotification($findComment));
            }
        }

        return new CommentResource($comment);
    }

    // Delete a comment
    public function destroy(Comment $comment)
    {
        if ($comment->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
