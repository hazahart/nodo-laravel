<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function index()
    {
        return response()->json(Institucion::all());
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'pais' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
        ]);

        $institucion = Institucion::create($datos);

        return response()->json($institucion, 201);
    }
}