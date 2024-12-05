<?php

namespace App\Http\Controllers\FrontEnd;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontEnd\StorePostRequest;
use App\Http\Requests\FrontEnd\UpdatePostRequest;
use App\Http\Resources\FrontEnd\PostResource;
use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostNotification;
use App\Traits\PostImagesUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Exception;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use PostImagesUpload;

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $posts = Post::whereUserId(Auth::user()->id)
            ->with('product', 'images')
            ->orderBy($sortField, $sortOrder)
            ->get();

        return PostResource::collection($posts);
    }

    public function show($postId)
    {
        $user = Auth::user();

        // Find the post that belongs to the user
        $post = Post::findOrFail($postId)->where('user_id', $user->id)->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post does not belong to the user',
            ], 200);
        }
        $post->load(['product', 'images']);
        return new PostResource($post);
    }

    public function publicShow(Post $post)
    {
        $post->load(['product', 'images']);
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $message = "Hello, this is a test notification!";
        event(new NotificationEvent($message));
        // Start a transaction
        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $validated['user_id'] = Auth::guard('user')->user()->id;
            unset($validated['images']);

            $post = Post::create($validated);

            // Handle multiple images
            if ($request->hasFile('images')) {
                // Use the ImageUpload trait
                $this->PostImagesUpload($request->file('images'), $post->id,'posts/' . $post->id . '/images');
            }

            // Find all admins with the 'view-posts' permission
            $admins = Admin::permission('view-products')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PostNotification($post, 'created'));
            }

            // Notify the user about the new post
            $user = auth()->user();
            $user->notify(new PostNotification($post, 'created'));

            // Commit the transaction
            DB::commit();

            return new PostResource($post->load(['user', 'product', 'images']));
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error for debugging
            Log::error('Product creation failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while creating the product',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();
        $post->update($validated);

        // Find all admins with the 'view-posts' permission
        $admins = User::permission('view-products')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PostNotification($post, 'updated'));
        }

        // Notify the user about the new post
        $user = auth()->user();
        $user->notify(new PostNotification($post, 'updated'));

        return new PostResource($post->load(['user', 'product']));
    }

    public function destroy(Post $post)
    {
        $images = $post->images;
        $post->delete();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function getSavedPosts()
    {
        $user =  Auth::guard('user')->user();
        return PostResource::collection($user->savedPosts->load('images'));
    }

    public function savePost(Request $request, $postId)
    {
        $user = Auth::guard('user')->user();
        $post = Post::findOrFail($postId);

        // Attach the post to the user’s saved posts if not already saved
        if (!$user->savedPosts()->where('post_id', $post->id)->exists()) {
            $user->savedPosts()->attach($post);
        }

        return response()->json(['message' => 'Post saved successfully.']);
    }

    public function unsavePost(Request $request, $postId)
    {
        $user =  Auth::guard('user')->user();
        $post = Post::findOrFail($postId);

        // Detach the post from the user’s saved posts if saved
        $user->savedPosts()->detach($post);

        return response()->json(['message' => 'Post unsaved successfully.']);
    }
}
