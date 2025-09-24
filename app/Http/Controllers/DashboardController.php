<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Agora o método apenas retorna a view, sem passar nenhum dado.
        return view('dashboard');
    }
}
