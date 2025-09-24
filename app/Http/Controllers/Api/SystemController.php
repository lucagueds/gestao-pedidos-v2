<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemController extends Controller
{
    public function syncOrders()
    {
        // Envia o comando para a fila para ser executado em segundo plano
        Artisan::queue('sync:orders', ['--force-items' => true]);

        return response()->json([
            'message' => 'A sincronização completa de pedidos foi iniciada em segundo plano.'
        ]);
    }
}
