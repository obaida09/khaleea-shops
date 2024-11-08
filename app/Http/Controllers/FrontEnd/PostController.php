<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\FrontEnd\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use App\Notifications\PostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {

        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $posts = Post::whereUserId(Auth::user()->id)
            ->with('product')
            ->orderBy($sortField, $sortOrder)
            ->get();

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load(['product', 'images']);
        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;

        $post = Post::create($validated);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('posts', 'public');
                // Create PostImage entry
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        // Find all admins with the 'view-posts' permission
        $admins = User::permission('view-products')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PostNotification($post, 'created'));
        }

        // Notify the user about the new post
        $user = auth()->user();
        $user->notify(new PostNotification($post, 'created'));

        return new PostResource($post->load(['user', 'product']));
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
        $post->delete();

        // Find all admins with the 'view-posts' permission
        $admins = User::permission('view-products')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PostNotification($post, 'deleted'));
        }

        // Notify the user about the new post
        $user = auth()->user();
        $user->notify(new PostNotification($post, 'deleted'));
        return response()->json(['message' => 'Post deleted successfully']);
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
