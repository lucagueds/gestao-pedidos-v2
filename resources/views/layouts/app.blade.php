<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DucaToys - Gestão de Pedidos</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased">
        <div class="flex h-full">
            <aside class="flex flex-col w-60 border-r border-border-gray bg-white">
                {{-- Logo --}}
                <div class="flex h-16 shrink-0 items-center justify-center border-b border-border-gray">
                    <h2 class="text-2xl font-bold text-tiny-blue">DucaToys</h2>
                </div>

                {{-- Links de Navegação --}}
                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-tiny-blue font-semibold border-l-4 border-tiny-blue' : 'text-text-light hover:bg-gray-100 hover:text-text-dark' }}">
                                <x-heroicon-o-home class="h-3 w-3 mr-2" /> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.index') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('orders.index') ? 'bg-blue-50 text-tiny-blue font-semibold border-l-4 border-tiny-blue' : 'text-text-light hover:bg-gray-100 hover:text-text-dark' }}">
                                <x-heroicon-o-shopping-cart class="h-3 w-3 mr-2" /> Pedidos
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors duration-150 text-text-light hover:bg-gray-100 hover:text-text-dark">
                                <x-heroicon-o-tag class="h-3 w-3 mr-2" /> Produtos
                            </a>
                        </li>
                    </ul>
                </nav>

                {{-- Rodapé da Sidebar com Usuário e Logout --}}
                <div class="border-t border-border-gray p-2">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-text-dark">{{ Auth::user()->name ?? 'Usuário' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Sair" class="p-2 text-text-light rounded-full hover:bg-gray-200">
                                <x-heroicon-o-arrow-right-on-rectangle class="h-3 w-3" />
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="flex-1 overflow-y-auto">
                @yield('content')
            </div>
        </div>
    </body>
</html>
