<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
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

        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('filterByRole')) {
            $query->role($request->filterByRole);
        }

        $users = $query->orderBy($sortField, $sortOrder)->paginate(10);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());

        // Assign the "user" role to the newly created user
        $user->assignRole('user');

        return response()->json([
            'message' => 'User Created',
        ], 201);
    }


    public function show(User $user)
    {
        return response()->json([
            'data' => new UserResource($user),
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->all();
        unset($data['role']);

        // Add Password to data
        trim($request->password) != '' ? $data['password'] = bcrypt($request->password) : '';
        $user->update($data);

        // Assign role if provided
        if ($request->has('role')) {
            $user->syncRoles($request->role);
        }

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User Updated',
        ], 201);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
