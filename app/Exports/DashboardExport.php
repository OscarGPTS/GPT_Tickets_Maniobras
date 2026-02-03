<?php

namespace App\Exports;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DashboardExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ResumenGeneralSheet(),
            new TicketsPorEstadoSheet(),
            new TicketsPorUsuarioSheet(),
            new RendimientoAlmacenSheet(),
            new EncuestasSheet(),
        ];
    }
}

/**
 * Hoja 1: Resumen General
 */
class ResumenGeneralSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return collect([
            [
                'seccion' => 'Usuarios',
                'metrica' => 'Total de Usuarios',
                'valor' => User::count(),
            ],
            [
                'seccion' => 'Usuarios',
                'metrica' => 'Administradores',
                'valor' => User::role('admin')->count(),
            ],
            [
                'seccion' => 'Usuarios',
                'metrica' => 'Personal de Almacén',
                'valor' => User::role('almacen')->count(),
            ],
            [
                'seccion' => 'Usuarios',
                'metrica' => 'Solicitantes',
                'valor' => User::role('solicitante')->count(),
            ],
            [
                'seccion' => 'Tickets',
                'metrica' => 'Total de Tickets',
                'valor' => Ticket::count(),
            ],
            [
                'seccion' => 'Tickets',
                'metrica' => 'Pendientes',
                'valor' => Ticket::where('status', 'pendiente')->count(),
            ],
            [
                'seccion' => 'Tickets',
                'metrica' => 'En Proceso',
                'valor' => Ticket::where('status', 'en_proceso')->count(),
            ],
            [
                'seccion' => 'Tickets',
                'metrica' => 'Finalizados',
                'valor' => Ticket::where('status', 'finalizado')->count(),
            ],
            [
                'seccion' => 'Tickets',
                'metrica' => 'Cancelados',
                'valor' => Ticket::where('status', 'cancelado')->count(),
            ],
            [
                'seccion' => 'Encuestas',
                'metrica' => 'Total de Encuestas',
                'valor' => Survey::count(),
            ],
            [
                'seccion' => 'Encuestas',
                'metrica' => 'Completadas',
                'valor' => Survey::whereNotNull('completed_at')->count(),
            ],
            [
                'seccion' => 'Encuestas',
                'metrica' => 'Pendientes',
                'valor' => Survey::whereNull('completed_at')->count(),
            ],
            [
                'seccion' => 'Encuestas',
                'metrica' => 'Calificación Promedio',
                'valor' => round(Survey::whereNotNull('completed_at')->avg('rating') ?? 0, 2),
            ],
        ]);
    }

    public function title(): string
    {
        return 'Resumen General';
    }

    public function headings(): array
    {
        return [
            'Sección',
            'Métrica',
            'Valor',
        ];
    }

    public function map($row): array
    {
        return [
            $row['seccion'],
            $row['metrica'],
            $row['valor'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 15,
        ];
    }
}

/**
 * Hoja 2: Tickets por Estado
 */
class TicketsPorEstadoSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Ticket::with(['user', 'assignedTo'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function title(): string
    {
        return 'Tickets por Estado';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Título',
            'Solicitante',
            'Asignado a',
            'Estado',
            'Fecha Creación',
            'Fecha Asignación',
            'Fecha Completado',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->formatted_code,
            $ticket->title,
            $ticket->user->name ?? 'N/A',
            $ticket->assignedTo->name ?? 'Sin asignar',
            ucfirst(str_replace('_', ' ', $ticket->status)),
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->assigned_at ? $ticket->assigned_at->format('d/m/Y H:i') : 'N/A',
            $ticket->completed_at ? $ticket->completed_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '70AD47']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 40,
            'D' => 25,
            'E' => 25,
            'F' => 15,
            'G' => 18,
            'H' => 18,
            'I' => 18,
        ];
    }
}

/**
 * Hoja 3: Tickets por Usuario Solicitante
 */
class TicketsPorUsuarioSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return User::role('solicitante')
            ->withCount([
                'tickets',
                'tickets as tickets_pendientes' => function ($query) {
                    $query->where('status', 'pendiente');
                },
                'tickets as tickets_en_proceso' => function ($query) {
                    $query->where('status', 'en_proceso');
                },
                'tickets as tickets_finalizados' => function ($query) {
                    $query->where('status', 'finalizado');
                },
                'tickets as tickets_cancelados' => function ($query) {
                    $query->where('status', 'cancelado');
                },
            ])
            ->get();
    }

    public function title(): string
    {
        return 'Tickets por Solicitante';
    }

    public function headings(): array
    {
        return [
            'Usuario',
            'Email',
            'Total Tickets',
            'Pendientes',
            'En Proceso',
            'Finalizados',
            'Cancelados',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->tickets_count,
            $user->tickets_pendientes,
            $user->tickets_en_proceso,
            $user->tickets_finalizados,
            $user->tickets_cancelados,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFC000']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }
}

/**
 * Hoja 4: Rendimiento del Personal de Almacén
 */
class RendimientoAlmacenSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        $almacenMembers = User::role('almacen')->get();
        $stats = [];

        foreach ($almacenMembers as $member) {
            $memberTickets = Ticket::where('assigned_to', $member->id)->where('status', 'finalizado')->get();
            $memberSurveys = Survey::whereIn('ticket_id', $memberTickets->pluck('id'))
                                  ->whereNotNull('completed_at')
                                  ->get();
            
            $totalSurveys = $memberSurveys->count();
            $avgRating = $totalSurveys > 0 ? round($memberSurveys->avg('rating') ?? 0, 2) : 0;
            $satisfiedCount = $memberSurveys->where('rating', '>=', 4)->count();
            $satisfactionPercentage = $totalSurveys > 0 ? round(($satisfiedCount / $totalSurveys) * 100, 1) : 0;
            
            $stats[] = [
                'name' => $member->name,
                'email' => $member->email,
                'total_tickets' => Ticket::where('assigned_to', $member->id)->count(),
                'tickets_pendientes' => Ticket::where('assigned_to', $member->id)->where('status', 'pendiente')->count(),
                'tickets_en_proceso' => Ticket::where('assigned_to', $member->id)->where('status', 'en_proceso')->count(),
                'completed_tickets' => $memberTickets->count(),
                'total_surveys' => $totalSurveys,
                'average_rating' => $avgRating,
                'satisfaction_percentage' => $satisfactionPercentage,
            ];
        }

        return collect($stats)->sortByDesc('satisfaction_percentage');
    }

    public function title(): string
    {
        return 'Rendimiento Almacén';
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Email',
            'Total Asignados',
            'Pendientes',
            'En Proceso',
            'Completados',
            'Encuestas',
            'Calificación Promedio',
            '% Satisfacción',
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['email'],
            $row['total_tickets'],
            $row['tickets_pendientes'],
            $row['tickets_en_proceso'],
            $row['completed_tickets'],
            $row['total_surveys'],
            $row['average_rating'],
            $row['satisfaction_percentage'] . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5B9BD5']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 15,
        ];
    }
}

/**
 * Hoja 5: Encuestas de Satisfacción
 */
class EncuestasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Survey::with(['ticket.user', 'ticket.assignedTo'])
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function title(): string
    {
        return 'Encuestas';
    }

    public function headings(): array
    {
        return [
            'ID Ticket',
            'Código Ticket',
            'Solicitante',
            'Atendido por',
            'Calificación',
            'Comentarios',
            'Fecha Completado',
        ];
    }

    public function map($survey): array
    {
        return [
            $survey->ticket->id ?? 'N/A',
            $survey->ticket->formatted_code ?? 'N/A',
            $survey->ticket->user->name ?? 'N/A',
            $survey->ticket->assignedTo->name ?? 'N/A',
            $survey->rating,
            $survey->comments ?? 'Sin comentarios',
            $survey->completed_at ? $survey->completed_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'ED7D31']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 15,
            'C' => 25,
            'D' => 25,
            'E' => 12,
            'F' => 50,
            'G' => 18,
        ];
    }
}
