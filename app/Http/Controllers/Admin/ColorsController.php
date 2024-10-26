<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Category;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ColorsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-colors', only: ['update']),
            new Middleware('can:delete-colors', only: ['destroy']),
            new Middleware('can:create-colors', only: ['store']),
            new Middleware('can:view-colors', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Color::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $colors = $query->orderBy($sortField, $sortOrder)->paginate(10);
        return ColorResource::collection($colors);
    }

    public function store(StoreColorRequest $request)
    {
        $data = $request->validated();
        Color::create($data);
        return response()->json(['message' => 'Category Created'], 201);
    }

    public function show(Color $color)
    {
        return new ColorResource($color->load('products'));
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        $color->update($request->validated());
        return new ColorResource($color);
    }

    public function destroy(Color $color)
    {
        $color->delete();
        return response()->noContent();
    }
}
