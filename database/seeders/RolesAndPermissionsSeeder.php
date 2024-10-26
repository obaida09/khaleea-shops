<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the permissions
        $permissions = [
            'manage-users','view-users','edit-users','create-users','delete-users',

            'manage-categories','view-categories','edit-categories','create-categories','delete-categories',

            'manage-tags','view-tags','edit-tags','create-tags','delete-tags',

            'manage-products','view-products','edit-products','create-products','delete-products',

            'manage-posts','view-posts','edit-posts','create-posts','delete-posts',

            'manage-coupons','view-coupons','edit-coupons','create-coupons','delete-coupons',

            'manage-orders','view-orders','edit-orders','create-orders','delete-orders',

            'manage-colors','view-colors','edit-colors','create-colors','delete-colors',

            'manage-sizes','view-sizes','edit-sizes','create-sizes','delete-sizes',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'supervisor', 'guard_name' => 'api']);
        Role::create(['name' => 'user', 'guard_name' => 'api']);

        $admin->givePermissionTo(Permission::all());

        $user = User::factory()->create([
            'name' => 'Khaleea',
            'email' => 'khaleea.com',
            'mobile' => '07724389401',
            'password' => Hash::make('obeda2001'),
        ]);

        $user->assignRole($admin);
    }
}
