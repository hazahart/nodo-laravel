<?php

namespace App\Http\Controllers;

use App\Models\NivelesGrado;

class CatalogoController extends Controller
{
    public function nivelesGrado()
    {
        return response()->json(NivelesGrado::all());
    }
}