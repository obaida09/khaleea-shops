<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-categories', only: ['update']),
            new Middleware('can:delete-categories', only: ['destroy']),
            new Middleware('can:create-categories', only: ['store']),
            new Middleware('can:view-categories', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        Category::create($data);
        return response()->json(['message' => 'Category Created'], 201);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if (!has_permission('edit-categories')) {
            return response()->json(['message' => 'User does not have permission to Vist this Url'], 403);
        }
        $data = $request->validated();
        $category->update($data);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Category Updated'
        ], 201);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
