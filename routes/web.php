<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site institucional — Simone Gomes
|--------------------------------------------------------------------------
| Site público, página única. Sem autenticação.
*/

Route::get('/', fn () => view('home'))->name('home');
