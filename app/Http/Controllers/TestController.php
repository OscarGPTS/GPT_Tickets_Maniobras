<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
