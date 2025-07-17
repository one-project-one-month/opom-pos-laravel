<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //  1. Define resources for resource permissions
        $resources = [
            'brand',
            'category',
            'customer',
            'discount_item',
            'order',
            'order_item',
            'payment',
            'product',
            'user',
            'role',
            'activity_log',
        ];

        // 2. Create resource permissions
        foreach ($resources as $resource) {
            Permission::firstOrCreate(['name' => "view_{$resource}"]);
            Permission::firstOrCreate(['name' => "view_any_{$resource}"]);
            Permission::firstOrCreate(['name' => "create_{$resource}"]);
            Permission::firstOrCreate(['name' => "update_{$resource}"]);
            Permission::firstOrCreate(['name' => "delete_{$resource}"]);
            Permission::firstOrCreate(['name' => "delete_any_{$resource}"]);
        }

        //  3. Create custom POS permissions (grouped by prefix "pos::")
        $customPermissions = [
            'pos::access_pos',
            'pos::view_reports',
            'pos::manage_inventory',
            'pos::process_refunds',
            'pos::apply_discounts',
            'pos::view_sales_analytics',
            'pos::manage_settings',
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create Super Admin Role and assign Full access
        $this->createUser('Super Admin', 'superadmin@pos.com', 'super_admin');
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 4. Create roles (only, no assignment)
        $roles = [ 'admin', 'manager', 'cashier', 'inventory_manager'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        //  5. Create users with roles
        $this->createUser('Admin User', 'admin@pos.com', 'admin');
        $this->createUser('Manager User', 'manager@pos.com', 'manager');
        $this->createUser('Cashier User', 'cashier@pos.com', 'cashier');
        $this->createUser('Inventory Manager', 'inventory@pos.com', 'inventory_manager');
    }

    private function createUser($name, $email, $role)
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
                //'confirmed_at' => now(),
            ]
        );
        $user->assignRole($role);
    }
}
