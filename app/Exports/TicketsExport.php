<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Ticket::with(['user', 'assignedTo', 'survey']);

        // Aplicar filtros si existen
        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['priority'])) {
            $query->where('priority', $this->filters['priority']);
        }

        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['assigned_to'])) {
            $query->where('assigned_to', $this->filters['assigned_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descripción',
            'Estado',
            'Prioridad',
            'Solicitante',
            'Email Solicitante',
            'Asignado a',
            'Fecha Creación',
            'Fecha Actualización',
            'Fecha Completado',
            'Calificación',
            'Comentario Encuesta',
        ];
    }

    /**
     * @var Ticket $ticket
     */
    public function map($ticket): array
    {
        $statusLabels = [
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
        ];

        $priorityLabels = [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'urgente' => 'Urgente',
        ];

        return [
            $ticket->id,
            $ticket->title,
            $ticket->description,
            $statusLabels[$ticket->status] ?? $ticket->status,
            $priorityLabels[$ticket->priority] ?? $ticket->priority,
            $ticket->user->name ?? 'N/A',
            $ticket->user->email ?? 'N/A',
            $ticket->assignedTo->name ?? 'Sin asignar',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->updated_at->format('d/m/Y H:i'),
            $ticket->completed_at ? $ticket->completed_at->format('d/m/Y H:i') : 'N/A',
            $ticket->survey ? $ticket->survey->rating . '/5' : 'Sin calificar',
            $ticket->survey->comment ?? 'N/A',
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 30,  // Título
            'C' => 40,  // Descripción
            'D' => 15,  // Estado
            'E' => 12,  // Prioridad
            'F' => 25,  // Solicitante
            'G' => 30,  // Email
            'H' => 25,  // Asignado a
            'I' => 18,  // Fecha Creación
            'J' => 18,  // Fecha Actualización
            'K' => 18,  // Fecha Completado
            'L' => 12,  // Calificación
            'M' => 40,  // Comentario
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo del encabezado
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Indigo-600
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
