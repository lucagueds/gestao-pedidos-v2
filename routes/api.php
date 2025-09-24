<?php

use App\Http\Controllers\Api\OrderController; // Adicione esta linha
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController; // Adicione esta linha

use App\Http\Controllers\Api\SystemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rota de Login e Registro (Pública)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Rota de Logout (Protegida)
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/sync-orders', [SystemController::class, 'syncOrders']);
    Route::post('/orders/{order}/resync', [OrderController::class, 'resync']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::put('/products/{product}/prices', [ProductController::class, 'updatePrices']);
    Route::post('/products/{product}/resync', [ProductController::class, 'resync']);

    Route::get('/orders/{order}/generate-pdfs', [OrderController::class, 'generatePdf']);
    Route::get('/orders/{order}/download-pdf', [OrderController::class, 'downloadPdf']);

});
