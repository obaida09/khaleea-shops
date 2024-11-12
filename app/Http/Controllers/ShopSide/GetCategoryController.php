<?php

namespace App\Http\Controllers\ShopSide;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class GetCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereStatus(true)->select('id', 'name')->get();
        return $categories;
    }
}
