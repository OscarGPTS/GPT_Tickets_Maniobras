<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\LaravelPdf\Facades\Pdf;


class TestController extends Controller
{
    public function testPdf()
    {
        $data = [
            'title' => 'Test PDF',
            'content' => 'Este es un PDF generado con Spatie Laravel PDF.',
        ];

        return Pdf::view('pdf.test', $data)
            ->format('a4')
            ->margins(10, 10, 10, 10)
            ->name('test.pdf')
            ->download();
    }

    public function testEmail()
    {
        try {
            Mail::raw(
                'Este es un correo de prueba desde el Sistema de Tickets. ' .
                'Enviado desde: ' . config('mail.from.address') . ' el ' . now()->format('d/m/Y H:i:s'),
                function ($message) {
                    $message->to('ochavez@gptservices.com')
                        ->subject('Correo de Prueba - Sistema de Tickets');
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente a ochavez@gptservices.com',
                'from' => config('mail.from.address'),
                'from_name' => config('mail.from.name')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
