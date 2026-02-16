<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportRequestController extends Controller
{
    /**
     * Mostrar el formulario de solicitud de soporte
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Guardar la solicitud de soporte
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'request_type' => 'required|in:eliminar_datos,recuperar_contrasena,cambiar_email,consulta_general,otro',
            'description' => 'required|string|max:2000',
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'request_type.required' => 'Debe seleccionar un tipo de solicitud.',
            'request_type.in' => 'El tipo de solicitud no es válido.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            SupportRequest::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'request_type' => $request->request_type,
                'description' => $request->description,
                'status' => 'pendiente',
            ]);

            return back()->with('success', '¡Solicitud enviada exitosamente! Nos pondremos en contacto contigo pronto.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Hubo un error al enviar tu solicitud. Por favor intenta nuevamente.')
                ->withInput();
        }
    }
}

