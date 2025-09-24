@extends('layouts.app')

@section('content')
    <header class="flex items-center justify-between flex-shrink-0 px-8 py-3 bg-white border-b">
        <span class="font-semibold text-tiny-blue">Pedidos de Venda</span>
        <div class="flex items-center space-x-3">
            <button class="px-4 py-2 text-sm font-bold text-white rounded-md bg-tiny-blue hover:bg-tiny-blue-hover">Sincronizar Pedido</button>
        </div>
    </header>

    <main class="flex-1 p-4 pb-16">

        <div class="bg-white border rounded-md shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-bg-light">
                        <tr>
                            <th class="p-3 font-medium text-left text-xs text-text-light uppercase tracking-wider">N°</th>
                            <th class="p-3 font-medium text-left text-xs text-text-light uppercase tracking-wider">Data</th>
                            <th class="p-3 font-medium text-left text-xs text-text-light uppercase tracking-wider">Cliente</th>
                            <th class="p-3 font-medium text-center text-xs text-text-light uppercase tracking-wider">Total</th>
                            <th class="p-3 font-medium text-center text-xs text-text-light uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-gray-500">{{ $order->id }}</td>
                                <td class="p-3">{{ $order->order_date->format('d/m/Y') }}</td>
                                <td class="p-3 font-semibold text-gray-800">{{ $order->customer_name }}</td>
                                <td class="p-3 text-center">R$ {{ number_format($order->total_amount_cache, 2, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center">
                                        <button class="py-2 px-4 flex items-center text-white rounded-md bg-tiny-blue hover:bg-tiny-blue-hover">
                                            <x-heroicon-o-arrow-down-tray class="h-3 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="fixed bottom-0 left-60 right-0 z-10 flex items-center justify-between p-3 bg-white/80 border-t backdrop-blur-md">
        <div>{{-- Paginação virá aqui --}}</div>
        <div class="text-sm text-right text-text-light">
            <span><strong>{{ $orders->total() }}</strong> quantidade</span>
            <span class="ml-4"><strong>R$ {{ number_format($totalValue, 2, ',', '.') }}</strong> valor total</span>
        </div>
    </footer>
@endsection
