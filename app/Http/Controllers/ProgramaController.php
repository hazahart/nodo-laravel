<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\NivelesGrado;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index()
    {
        return response()->json(
            Programa::with('nivelGrado')->get()
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'         => 'required|string|max:255',
            'nivel_grado_id' => 'required|integer|exists:niveles_grados,id',
        ]);

        $programa = Programa::with('nivelGrado')->create($datos);

        return response()->json($programa, 201);
    }
}