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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // Create roles and assign permissions

        // Super Admin Role - Full access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role - Almost full access except some critical operations
        $admin =Role::firstOrCreate(['name' => 'admin']);

        // Manager Role - Management operations
        $manager = Role::firstOrCreate(['name' =>'manager']);

        // Cashier Role - POS operations only
        $cashier = Role::firstOrCreate(['name' => 'cashier']);

        // Inventory Manager Role - Inventory specific operations
        $inventoryManager = Role::firstOrCreate(['name' => 'inventory_manager']);

        // Create default users with roles

        // Super Admin User
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pos.com',
            'role_id' => 1,
            'password' => bcrypt('password'),
            'comfirmed_at' => now(),
        ]);
        $superAdminUser->assignRole('super_admin');

        // Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
            'role_id' =>2,
            'password' => bcrypt('password'),
            'comfirmed_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        // Manager User
        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@pos.com',
            'role_id' => 3,
            'password' => bcrypt('password'),
            'comfirmed_at' => now(),
        ]);
        $managerUser->assignRole('manager');

        // Cashier User
        $cashierUser = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@pos.com',
            'role_id' => 4,
            'password' => bcrypt('password'),
            'comfirmed_at' => now(),
        ]);
        $cashierUser->assignRole('cashier');

        // Inventory Manager User
        $inventoryUser = User::create([
            'name' => 'Inventory Manager',
            'email' => 'inventory@pos.com',
            'role_id' => 5,
            'password' => bcrypt('password'),
            'comfirmed_at' => now(),
        ]);
        $inventoryUser->assignRole('inventory_manager');

        //Re-cache after seeding
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}