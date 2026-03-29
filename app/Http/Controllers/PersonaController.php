<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function index()
    {
        return response()->json(Persona::all());
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'curp' => 'nullable|string|max:18|unique:personas',
            'correo' => 'nullable|email|max:150',
        ]);

        $persona = Persona::create($datos);

        return response()->json($persona, 201);
    }
}