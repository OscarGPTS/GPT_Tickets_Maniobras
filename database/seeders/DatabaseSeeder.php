<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder de roles y permisos
        $this->call([
            RoleSeeder::class,
            // TestUsersSeeder::class,  // Usuarios de prueba deshabilitados
            GooglePlayTestSeeder::class,  // Usuario de prueba para Google Play
        ]);
    }
}
