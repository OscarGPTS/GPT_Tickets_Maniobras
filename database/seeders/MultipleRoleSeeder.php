<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MultipleRoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'access-admin-panel',
            'view-users',
            'manage-users',
            'assign-roles',
            'view-statistics',
            'create-tickets',
            'view-own-tickets',
            'view-all-tickets',
            'assign-tickets',
            'complete-tickets',
            'delete-tickets',
            'upload-images',
            'create-surveys',
            'view-surveys',
            'complete-surveys',
            'view-survey-results',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $almacenRole = Role::firstOrCreate(['name' => 'almacen', 'guard_name' => 'web']);
        $solicitanteRole = Role::firstOrCreate(['name' => 'solicitante', 'guard_name' => 'web']);

        // Asignar permisos a roles
        
        // Administrador: todos los permisos
        $adminRole->syncPermissions($permissions);

        // Personal de Almacén: permisos específicos para gestión de tickets
        $almacenRole->syncPermissions([
            'view-all-tickets',
            'assign-tickets',
            'complete-tickets',
            'upload-images',
            'create-surveys',
            'view-surveys',
            'view-survey-results',
        ]);

        // Solicitante: permisos básicos
        $solicitanteRole->syncPermissions([
            'create-tickets',
            'view-own-tickets',
            'upload-images',
            'complete-surveys',
        ]);

        // Asegurar que el usuario ID 1 sea administrador
        $user1 = User::find(1);
        if ($user1) {
            // Agregar rol admin (manteniendo otros roles si los tiene)
            $user1->assignRole('admin');
            $this->command->info("✅ Usuario ID 1 ({$user1->name}) asignado como administrador");
        }

        // Asignar rol 'solicitante' a todos los usuarios que no tengan roles
        $usersWithoutRoles = User::doesntHave('roles')->get();
        foreach ($usersWithoutRoles as $user) {
            $user->assignRole('solicitante');
        }

        $this->command->info('✅ Roles múltiples y permisos configurados exitosamente');
        $this->command->info('✅ Usuarios sin roles asignados como solicitantes');
    }
}Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MultipleRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
