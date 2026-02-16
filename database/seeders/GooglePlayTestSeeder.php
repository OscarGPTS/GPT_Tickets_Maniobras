<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GooglePlayTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea un usuario de prueba para Google Play con tickets de ejemplo
     */
    public function run(): void
    {
        // Buscar usuario de prueba para Google Play
        $testUser = User::where('email', 'google-test@gmail.com')->first();

        if (!$testUser) {
            $this->command->error('❌ Error: El usuario google-test@gmail.com no existe.');
            $this->command->info('Por favor, primero inicia sesión en la app móvil con este usuario.');
            return;
        }

        $this->command->info("✅ Usuario encontrado: {$testUser->name} ({$testUser->email})");

        // Buscar usuarios de almacén disponibles
        $almacenUsers = User::role('almacen')->get();

        if ($almacenUsers->isEmpty()) {
            $this->command->error('❌ Error: No hay usuarios con rol de almacén.');
            $this->command->info('Por favor, crea usuarios de almacén primero.');
            return;
        }

        $this->command->info("✅ Se encontraron {$almacenUsers->count()} usuarios de almacén");

        // Eliminar tickets anteriores del usuario de prueba (para evitar duplicados)
        Ticket::where('user_id', $testUser->id)->delete();

        // Crear tickets de prueba en diferentes estados (2 de cada uno)
        $ticketsPendientes = 0;
        $ticketsEnProceso = 0;
        $ticketsFinalizados = 0;
        
        // 1. Ticket pendiente - Mover tubería PVC
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Mover 25 tubos PVC de 3 metros',
            'description' => 'Solicito mover 25 tubos de PVC de 2 pulgadas (3 metros cada uno) desde el almacén principal hacia el área de construcción (Edificio B, piso 3). Peso aproximado 150 kg.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subDays(2),
        ]);
        $ticketsPendientes++;

        // 2. Ticket pendiente - Mover rollos de teflón
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Mover 3 cajas de cinta teflón',
            'description' => 'Trasladar 3 cajas (30 rollos por caja) de cinta de teflón desde área de recepción hacia rack B-22 del almacén. Las cajas llegaron hoy en la mañana.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subHours(4),
        ]);
        $ticketsPendientes++;

        // 3. Ticket en proceso - Mover válvulas
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Trasladar 12 válvulas de paso',
            'description' => 'Mover 12 válvulas de paso de 1/2 pulgada desde el rack A-15 del almacén hacia el taller de mantenimiento en planta baja. Material frágil, requiere cuidado.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subHours(6),
            'created_at' => now()->subHours(8),
        ]);
        $ticketsEnProceso++;

        // 4. Ticket en proceso - Mover tubería galvanizada
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Trasladar 15 tramos de tubería galvanizada',
            'description' => 'Mover 15 tramos de tubería galvanizada de 3/4 pulgada (6 metros cada uno) desde patio de materiales hacia bodega cubierta. Material pesado, requiere diablito o montacargas. Peso total aprox. 280 kg.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subDays(1),
            'created_at' => now()->subDays(1)->subHours(2),
        ]);
        $ticketsEnProceso++;

        // 5. Ticket finalizado - Mover conectores
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Trasladar caja de conectores',
            'description' => 'Mover 1 caja conteniendo 50 conectores rápidos desde bodega externa hacia almacén principal, ubicación C-08. Peso aprox. 25 kg.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Movimiento completado. Material trasladado y ubicado en estante C-08. Inventario actualizado en sistema.',
            'assigned_at' => now()->subDays(4),
            'completed_at' => now()->subDays(3),
            'created_at' => now()->subDays(5),
        ]);
        $ticketsFinalizados++;

        // 6. Ticket finalizado - Mover llaves
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Trasladar 8 llaves mezcladoras',
            'description' => 'Mover 8 llaves mezcladoras cromadas con accesorios desde área de descarga hacia almacén general, rack D-12. Incluye flexibles y empaques. Material nuevo en caja.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Traslado completado. 8 llaves mezcladoras ubicadas en rack D-12. Material revisado, empacado en buen estado.',
            'assigned_at' => now()->subDays(2),
            'completed_at' => now()->subDay(),
            'created_at' => now()->subDays(3),
        ]);
        $ticketsFinalizados++;

        $totalTickets = $ticketsPendientes + $ticketsEnProceso + $ticketsFinalizados;
        
        $this->command->info("✅ Se crearon {$totalTickets} tickets de prueba para google-test@gmail.com");
        $this->command->info("   - {$ticketsPendientes} tickets pendientes");
        $this->command->info("   - {$ticketsEnProceso} tickets en proceso");
        $this->command->info("   - {$ticketsFinalizados} tickets finalizados sin calificar");
    }
}
