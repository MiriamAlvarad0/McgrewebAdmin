<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users:view',
            'users:create',
            'users:edit',
            'users:delete',
            'maquinaria:view',
            'maquinaria:create',
            'maquinaria:edit',
            'maquinaria:delete',
            // permisos del admin para manipular el crud de inventaario:
            'categorias:view',
            'categorias:create',
            'categorias:edit',
            'categorias:delete',
            'subcategorias:view',
            'subcategorias:create',
            'subcategorias:edit',
            'subcategorias:delete'
        ];

        foreach ($permissions as $permission) {
           Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $roleGuest = Role::where('name', '=', 'Guest')->first();
        $roleGuest->givePermissionTo(['users:view', 'maquinaria:view']);

        $roleUser = Role::where('name', '=', 'User')->first();
        $roleUser->givePermissionTo([
            'users:view',
            'users:create',
            'users:edit',
            'maquinaria:view',
            'maquinaria:create',
            'maquinaria:edit',
            'categorias:view',
            'categorias:create',
            'categorias:edit',
            'subcategorias:view',
            'subcategorias:create',
            'subcategorias:edit'
        ]);

        $roleAdmin = Role::where('name', '=', 'Admin')->first();
        $roleAdmin->givePermissionTo([
            'users:view',
            'users:create',
            'users:edit',
            'users:delete',
            'maquinaria:view',
            'maquinaria:create',
            'maquinaria:edit',
            'maquinaria:delete',
            'categorias:view',
            'categorias:create',
            'categorias:edit',
            'categorias:delete',
            'subcategorias:view',
            'subcategorias:create',
            'subcategorias:edit',
            'subcategorias:delete'
        ]);
    }
}
