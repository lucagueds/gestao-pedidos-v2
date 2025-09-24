@extends('layouts.app')

@section('content')
    <header class="flex-shrink-0 px-8 py-4 bg-white border-b border-border-gray">
        <h1 class="text-xl font-bold text-text-dark">Bem-vindo ao Sistema</h1>
    </header>

    <main class="flex-1 p-4">
        <div class="p-12 text-center bg-white border rounded-lg shadow-sm border-border-gray">
            <h2 class="text-3xl font-bold text-text-dark">
                Sistema de Gestão de Pedidos
            </h2>

            <p class="max-w-2xl mx-auto mt-4 text-base text-text-light">
                Este é o seu painel central para gerenciar, sincronizar e customizar seus pedidos. Utilize o menu lateral para navegar entre as seções de Pedidos e Produtos.
            </p>

            <div class="mt-8">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-6 py-3 font-semibold text-white uppercase tracking-widest text-sm rounded-md bg-tiny-blue hover:bg-tiny-blue-hover">
                    Ver Pedidos
                </a>
            </div>
        </div>
    </main>
@endsection
