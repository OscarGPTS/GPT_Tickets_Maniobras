<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hacer el campo password nullable para usuarios OAuth
            $table->string('password')->nullable()->change();
            
            // Renombrar auth0_id a provider_id (sin números)
            $table->renameColumn('auth0_id', 'provider_id');
            
            // Agregar campo para imagen del usuario
            $table->string('avatar')->nullable()->after('provider_id');
            
            // Agregar campo para identificar el proveedor de autenticación
            $table->string('provider')->default('google')->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir cambios
            $table->string('password')->nullable(false)->change();
            $table->renameColumn('provider_id', 'auth0_id');
            $table->dropColumn(['avatar', 'provider']);
        });
    }
};
