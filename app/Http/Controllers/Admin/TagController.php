<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TagController extends Controller implements HasMiddleware
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

        $query = Tag::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tags = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return TagResource::collection($tags);
    }

    public function store(StoreTagRequest $request)
    {
       Tag::create($request->all());

        return response()->json([
            'message' => 'Tag Created',
        ], 201);
    }

    public function show(Tag $tag)
    {
        return response()->json([
            'data' =>  new TagResource($tag),
            'message' => 'Tag Created',
        ], 201);
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->all());

        return response()->json([
            'data' => new TagResource($tag),
            'message' => 'Tag Updated',
        ], 201);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(null, 204);
    }
}
