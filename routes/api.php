<?php

use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\CatalogoController;
use Illuminate\Support\Facades\Route;

Route::get('/chain', [BlockchainController::class, 'chain']);
Route::post('/mine', [BlockchainController::class, 'mine']);
Route::get('/nodes/resolve', [BlockchainController::class, 'resolve']);
Route::post('/blocks/receive', [BlockchainController::class, 'recibirBloque']);

Route::post('/transactions', [TransactionController::class, 'store']);
Route::post('/transactions/receive', [TransactionController::class, 'store']);
Route::get('/transactions', [TransactionController::class, 'index']);

Route::post('/nodes/register', [NodeController::class, 'register']);
Route::get('/nodes', [NodeController::class, 'index']);

Route::get('/eventos', [EventoController::class, 'stream']);

Route::get('/personas', [PersonaController::class, 'index']);
Route::post('/personas', [PersonaController::class, 'store']);

Route::get('/instituciones', [InstitucionController::class, 'index']);
Route::post('/instituciones', [InstitucionController::class, 'store']);

Route::get('/programas', [ProgramaController::class, 'index']);
Route::post('/programas', [ProgramaController::class, 'store']);

Route::get('/niveles-grado', [CatalogoController::class, 'nivelesGrado']);
