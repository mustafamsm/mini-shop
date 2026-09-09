<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $permissions = [
        //     'view products',
        //     'create products',
        //     'edit products',
        //     'delete products',
        //     'manage orders',
        //     'manage users',

        // ];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());

        Role::create(['name' => 'staff'])->givePermissionTo([
            'view products',
            'create products',
            'edit products',
        ]);
        Role::create(['name' => 'customer']);
    }
}
