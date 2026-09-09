<?php

use App\Http\Controllers\AgendamentoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site institucional — Simone Gomes
|--------------------------------------------------------------------------
| Site público, página única. Sem autenticação.
*/

Route::get('/', fn () => view('home'))->name('home');

// Horários livres do dia — consumido pelo Alpine (fetch) na seção de contato.
Route::get('/disponibilidade', [AgendamentoController::class, 'disponibilidade'])
    ->name('disponibilidade');

// Envio do formulário "Reserve sua data".
Route::post('/agendar', [AgendamentoController::class, 'store'])
    ->name('agendar');
