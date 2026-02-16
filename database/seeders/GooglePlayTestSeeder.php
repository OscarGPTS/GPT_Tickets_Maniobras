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

        // Crear tickets de prueba en diferentes estados
        $ticketsPendientes = 0;
        $ticketsEnProceso = 0;
        $ticketsFinalizados = 0;
        
        // 1. Ticket pendiente - Codos de PVC
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Solicitud de codos de PVC de 2 pulgadas',
            'description' => 'Necesito 10 codos de PVC de 2 pulgadas para reparación de tubería en el área de producción. Se requieren con urgencia para terminar el trabajo de mantenimiento.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subDays(3),
        ]);
        $ticketsPendientes++;

        // 2. Ticket en proceso - Válvulas de paso
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Válvulas de paso de 1/2 pulgada',
            'description' => 'Se requieren 5 válvulas de paso de 1/2 pulgada de bronce para reemplazo en sistema hidráulico del edificio norte.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subDays(1),
            'created_at' => now()->subDays(2),
        ]);
        $ticketsEnProceso++;

        // 3. Ticket finalizado - Conectores rápidos
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Conectores rápidos para manguera',
            'description' => 'Solicito 8 conectores rápidos para manguera de aire comprimido en taller de mantenimiento.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Se entregaron 8 conectores rápidos de 1/4 NPT. Material verificado y en buen estado.',
            'assigned_at' => now()->subDays(5),
            'completed_at' => now()->subDays(4),
            'created_at' => now()->subDays(6),
        ]);
        $ticketsFinalizados++;

        // 4. Ticket pendiente - Cintas de teflón
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Cinta de teflón y pasta selladora',
            'description' => 'Requiero 15 rollos de cinta de teflón y 3 botes de pasta selladora para trabajos de instalación de tubería nueva.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subHours(12),
        ]);
        $ticketsPendientes++;

        // 5. Ticket en proceso - Tubería galvanizada
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Tubería galvanizada de 3/4 pulgada',
            'description' => 'Se necesitan 6 metros de tubería galvanizada de 3/4 pulgada para extensión de red hidráulica en área de servicios.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subHours(6),
            'created_at' => now()->subHours(8),
        ]);
        $ticketsEnProceso++;

        // 6. Ticket urgente en proceso - Fuga de agua
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => '🔴 URGENTE: Reparación de fuga en tubería principal',
            'description' => 'Emergencia: Fuga en tubería principal. Necesito abrazaderas de reparación tamaño 2 pulgadas, empaque de goma y tornillería. La fuga está causando inundación.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subMinutes(20),
            'created_at' => now()->subMinutes(25),
        ]);
        $ticketsEnProceso++;

        // 7. Ticket finalizado - Llaves mezcladoras
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Llaves mezcladoras para sanitarios',
            'description' => 'Solicitud de 3 llaves mezcladoras cromadas para reemplazo en sanitarios del edificio B. Incluir flexibles y empaques.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Se entregaron 3 llaves mezcladoras marca URREA con flexibles de 40cm y juego completo de empaques. Todo instalado correctamente.',
            'assigned_at' => now()->subDays(3),
            'completed_at' => now()->subDays(2),
            'created_at' => now()->subDays(4),
        ]);
        $ticketsFinalizados++;

        // 8. Ticket pendiente - Herramientas
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Juego de llaves Stillson',
            'description' => 'Requiero préstamo o asignación de juego de llaves Stillson (10, 14 y 18 pulgadas) para trabajos de mantenimiento de tubería de gran diámetro.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subHours(4),
        ]);
        $ticketsPendientes++;

        // 9. Ticket en proceso - Reducción de tubería
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Reducciones y niples de tubería',
            'description' => 'Necesito 4 reducciones bushing de 1" a 3/4", 6 niples de 4 pulgadas y 3 niples de 6 pulgadas para adaptación de sistema.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subHours(3),
            'created_at' => now()->subHours(5),
        ]);
        $ticketsEnProceso++;

        // 10. Ticket pendiente - Soportería
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Abrazaderas y soportes para tubería',
            'description' => 'Solicito 20 abrazaderas tipo omega de 1/2 pulgada, 15 de 3/4 pulgada y taquetes con tornillos para instalación de tubería en muros.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subHours(2),
        ]);
        $ticketsPendientes++;

        // 11. Ticket finalizado - Manómetros
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Manómetros para sistema de presión',
            'description' => 'Se requieren 2 manómetros de 0-160 PSI con conexión de 1/4 NPT para monitoreo de presión en sistema hidráulico.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Se entregaron 2 manómetros de glicerina marca WIKA calibrados y verificados. Presión de trabajo verificada.',
            'assigned_at' => now()->subDays(2),
            'completed_at' => now()->subDay(),
            'created_at' => now()->subDays(3),
        ]);
        $ticketsFinalizados++;

        // 12. Ticket en proceso - Soldadura
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Material para soldadura de tubería',
            'description' => 'Necesito 5 kilos de electrodo 6011 de 1/8 pulgada y 3 discos de corte para metal. Trabajo de reparación estructural en soportes.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subHours(10),
            'created_at' => now()->subHours(11),
        ]);
        $ticketsEnProceso++;

        // 13. Ticket pendiente - Filtros
        Ticket::create([
            'user_id' => $testUser->id,
            'title' => 'Filtros para sistema de agua',
            'description' => 'Requiero 2 cartuchos filtrantes de sedimento de 5 micras y 2 de carbón activado para mantenimiento preventivo de sistema de filtración.',
            'status' => Ticket::STATUS_PENDIENTE,
            'created_at' => now()->subMinutes(45),
        ]);
        $ticketsPendientes++;

        // 14. Ticket finalizado - Pegamento PVC
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Pegamento y limpiador para PVC',
            'description' => 'Solicito 2 botes de pegamento para PVC de 500ml y 1 bote de limpiador para instalación de red hidráulica nueva.',
            'status' => Ticket::STATUS_FINALIZADO,
            'work_evidence' => 'Se entregó pegamento Tangit y limpiador marca Pavco. Material en fecha vigente y condiciones óptimas.',
            'assigned_at' => now()->subDays(1)->subHours(8),
            'completed_at' => now()->subHours(18),
            'created_at' => now()->subDays(2),
        ]);
        $ticketsFinalizados++;

        // 15. Ticket en proceso - Bomba de agua
        Ticket::create([
            'user_id' => $testUser->id,
            'assigned_to' => $almacenUsers->random()->id,
            'title' => 'Repuestos para bomba centrífuga',
            'description' => 'La bomba del sistema de presión necesita mantenimiento. Requiero kit de sellos mecánicos, rodamientos y empaque para bomba de 1.5 HP.',
            'status' => Ticket::STATUS_EN_PROCESO,
            'assigned_at' => now()->subHours(15),
            'created_at' => now()->subDays(1),
        ]);
        $ticketsEnProceso++;

        $totalTickets = $ticketsPendientes + $ticketsEnProceso + $ticketsFinalizados;
        
        $this->command->info("✅ Se crearon {$totalTickets} tickets de prueba para google-test@gmail.com");
        $this->command->info("   - {$ticketsPendientes} tickets pendientes");
        $this->command->info("   - {$ticketsEnProceso} tickets en proceso");
        $this->command->info("   - {$ticketsFinalizados} tickets finalizados sin calificar");
    }
}
