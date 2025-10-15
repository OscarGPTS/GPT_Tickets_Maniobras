<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el ENUM del campo status para incluir 'cancelado'
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pendiente', 'en_proceso', 'finalizado', 'cancelado') DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el ENUM al estado original (sin 'cancelado')
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('pendiente', 'en_proceso', 'finalizado') DEFAULT 'pendiente'");
    }
};
