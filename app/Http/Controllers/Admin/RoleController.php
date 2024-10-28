<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(10);
        return RoleResource::collection($roles);
    }

    public function show($roleId)
    {
        // Find the role
        $role = Role::findOrFail($roleId);
        return new RoleResource($role->load('permissions'));
    }

    public function create(StoreRoleRequest $request)
    {
        // Create the role
        $role = Role::create(['name' => $request->name]);

        // Get the permissions from the request
        $permissions = $request->input('permissions');

        // Assign the permissions to the role
        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Role created and permissions assigned successfully',
            'role' => $role,
            'permissions' => $role->permissions
        ]);
    }

    public function update(UpdateRoleRequest $request, $roleId)
    {
        // Find the role
        $role = Role::findOrFail($roleId);

        // Update the role's name
        $role->name = $request->input('name');
        $role->save();

        // Get the permissions from the request
        $permissions = $request->input('permissions');

        // Update the permissions for the role
        // $role->syncPermissions($permissions);
        $role->givePermissionTo($permissions);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role,
            'permissions' => $role->permissions
        ]);
    }

    public function destroy($roleId)
    {
        // Find the role by UUID or ID
        $role = Role::findOrFail($roleId);

        // Delete the role
        $role->delete();

        // Return a response confirming deletion
        return response()->json([
            'message' => 'Role deleted successfully',
        ], 200);
    }

    public function getPermissions()
    {
        $permissions = Permission::with('roles:name')->paginate(10);
        return response()->json($permissions);
    }
}
