<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $almacenRole = Role::firstOrCreate(['name' => 'almacen']);
        $solicitanteRole = Role::firstOrCreate(['name' => 'solicitante']);

        // // Crear usuarios de prueba para cada rol
        // 
        // // ADMINISTRADORES
        // $admin1 = User::updateOrCreate(
        //     ['email' => 'admin@gptservices.com'],
        //     [
        //         'name' => 'Admin Principal',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $admin1->syncRoles([$adminRole]);
        //
        // $admin2 = User::updateOrCreate(
        //     ['email' => 'admin2@gptservices.com'],
        //     [
        //         'name' => 'Admin Secundario',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $admin2->syncRoles([$adminRole]);
        //
        // // PERSONAL DE ALMACÉN
        // $almacen1 = User::updateOrCreate(
        //     ['email' => 'almacen1@gptservices.com'],
        //     [
        //         'name' => 'Carlos Rodríguez',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $almacen1->syncRoles([$almacenRole]);
        //
        // $almacen2 = User::updateOrCreate(
        //     ['email' => 'almacen2@gptservices.com'],
        //     [
        //         'name' => 'María García',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $almacen2->syncRoles([$almacenRole]);
        //
        // $almacen3 = User::updateOrCreate(
        //     ['email' => 'almacen3@gptservices.com'],
        //     [
        //         'name' => 'José Hernández',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $almacen3->syncRoles([$almacenRole]);
        //
        // $almacen4 = User::updateOrCreate(
        //     ['email' => 'almacen4@gptservices.com'],
        //     [
        //         'name' => 'Ana Martínez',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $almacen4->syncRoles([$almacenRole]);
        //
        // // SOLICITANTES
        // $solicitante1 = User::updateOrCreate(
        //     ['email' => 'solicitante1@gptservices.com'],
        //     [
        //         'name' => 'Juan Pérez',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $solicitante1->syncRoles([$solicitanteRole]);
        //
        // $solicitante2 = User::updateOrCreate(
        //     ['email' => 'solicitante2@gptservices.com'],
        //     [
        //         'name' => 'Laura Sánchez',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $solicitante2->syncRoles([$solicitanteRole]);
        //
        // $solicitante3 = User::updateOrCreate(
        //     ['email' => 'solicitante3@gptservices.com'],
        //     [
        //         'name' => 'Roberto López',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $solicitante3->syncRoles([$solicitanteRole]);
        //
        // $solicitante4 = User::updateOrCreate(
        //     ['email' => 'solicitante4@gptservices.com'],
        //     [
        //         'name' => 'Patricia Ramírez',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $solicitante4->syncRoles([$solicitanteRole]);
        //
        // $solicitante5 = User::updateOrCreate(
        //     ['email' => 'solicitante5@gptservices.com'],
        //     [
        //         'name' => 'Miguel Torres',
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password123'),
        //     ]
        // );
        // $solicitante5->syncRoles([$solicitanteRole]);
        //
        // $this->command->info('Usuarios de prueba creados exitosamente:');
        // $this->command->info('');
        // $this->command->info('ADMINISTRADORES (password: password123):');
        // $this->command->info('  - admin@gptservices.com (Admin Principal)');
        // $this->command->info('  - admin2@gptservices.com (Admin Secundario)');
        // $this->command->info('');
        // $this->command->info('PERSONAL DE ALMACÉN (password: password123):');
        // $this->command->info('  - almacen1@gptservices.com (Carlos Rodríguez)');
        // $this->command->info('  - almacen2@gptservices.com (María García)');
        // $this->command->info('  - almacen3@gptservices.com (José Hernández)');
        // $this->command->info('  - almacen4@gptservices.com (Ana Martínez)');
        // $this->command->info('');
        // $this->command->info('SOLICITANTES (password: password123):');
        // $this->command->info('  - solicitante1@gptservices.com (Juan Pérez)');
        // $this->command->info('  - solicitante2@gptservices.com (Laura Sánchez)');
        // $this->command->info('  - solicitante3@gptservices.com (Roberto López)');
        // $this->command->info('  - solicitante4@gptservices.com (Patricia Ramírez)');
        // $this->command->info('  - solicitante5@gptservices.com (Miguel Torres)');

    }
}
