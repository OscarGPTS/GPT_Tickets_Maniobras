<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $almacenRole = Role::firstOrCreate(['name' => 'almacen']);
        $solicitanteRole = Role::firstOrCreate(['name' => 'solicitante']);

        // Crear permisos
        $permissions = [
            // Permisos de usuarios
            'manage-users',
            'assign-roles',
            'view-users',
            
            // Permisos de tickets
            'view-all-tickets',
            'assign-tickets',
            'complete-tickets',
            'create-tickets',
            'edit-own-tickets',
            'view-own-tickets',
            
            // Permisos de encuestas
            'view-all-surveys',
            'complete-surveys',
            
            // Permisos administrativos
            'access-admin-panel',
            'view-statistics',
            'manage-system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos a roles
        // Admin: todos los permisos
        $adminRole->syncPermissions(Permission::all());

        // Almacén: permisos relacionados con tickets y algunos administrativos
        $almacenRole->syncPermissions([
            'view-all-tickets',
            'assign-tickets', 
            'complete-tickets',
            'view-all-surveys',
            'view-statistics',
        ]);

        // Solicitante: permisos básicos para sus propios tickets y encuestas
        $solicitanteRole->syncPermissions([
            'create-tickets',
            'edit-own-tickets',
            'view-own-tickets',
            'complete-surveys',
        ]);

        // Asegurar que el usuario ID 1 sea administrador
        $user1 = User::find(1);
        if ($user1) {
            // Eliminar roles existentes y asignar solo admin
            $user1->syncRoles(['admin']);
            $this->command->info("✅ Usuario ID 1 ({$user1->name}) asignado como administrador");
        }

        // Asignar roles apropiados a usuarios existentes basándose en patrones
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            if ($user->id === 1) {
                continue; // Ya manejado arriba
            }
            
            // Si el usuario no tiene roles, asignar 'solicitante' por defecto
            if ($user->roles()->count() === 0) {
                $user->assignRole('solicitante');
            }
        }

        $this->command->info("✅ Roles y permisos creados exitosamente");
        $this->command->info("✅ Usuarios existentes asignados a roles apropiados");
    }
}
