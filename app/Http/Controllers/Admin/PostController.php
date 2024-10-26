<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-tags', only: ['update']),
            new Middleware('can:delete-tags', only: ['destroy']),
            new Middleware('can:create-tags', only: ['store']),
            new Middleware('can:view-tags', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {

        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Post::query()->with('user', 'product');
        $posts = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'product']);
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
        return new PostResource($post->load(['user', 'product']));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();
        $post->update($validated);

        return new PostResource($post->load(['user', 'product']));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
