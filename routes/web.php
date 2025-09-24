<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\DashboardController; // Adicione esta linha

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota principal: redireciona para o dashboard se logado, senão para o login.
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Alterado para a rota do dashboard
    }
    return redirect()->route('login');
});

// Agrupa todas as rotas que precisam de autenticação
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Nova rota
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
});

// Inclui as rotas de autenticação geradas pelo Breeze
require __DIR__.'/auth.php';
