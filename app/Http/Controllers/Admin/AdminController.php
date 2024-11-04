<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('can:edit-users', only: ['update']),
            new Middleware('can:delete-users', only: ['destroy']),
            new Middleware('can:create-users', only: ['store']),
            new Middleware('can:view-users', only: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $sortField = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default order 'asc'

        $query = Admin::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $admins = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return AdminResource::collection($admins);
    }

    public function store(StoreAdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        Admin::create($request->all());

        return response()->json([
            'message' => 'admin Created',
        ], 201);
    }


    public function show(Admin $admin)
    {
        $admin->load('roles');
        return new AdminResource($admin);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $data = $request->all();
        unset($data['role']);

        // Add Password to data
        trim($request->password) != '' ? $data['password'] = bcrypt($request->password) : '';
        $admin->update($data);

        // Assign role if provided
        if ($request->has('role')) {
            $admin->syncRoles($request->role);
        }

        return response()->json([
            'data' => new AdminResource($admin),
            'message' => 'admin Updated',
        ], 201);
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->json(null, 204);
    }
}

