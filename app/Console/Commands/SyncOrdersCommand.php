<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\TinyApiService;
use App\Traits\ManagesOrderItems; // 1. Importe o Trait
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncOrdersCommand extends Command
{
    use ManagesOrderItems; // 2. Use o Trait

    protected $signature = 'sync:orders {--force-items : Força a resincronização dos itens de cada pedido}';
    protected $description = 'Sincroniza os pedidos da API do Tiny para o banco de dados local.';

    public function handle(TinyApiService $tinyApiService)
    {
        $this->info('Iniciando a sincronização de pedidos...');
        $forceItems = $this->option('force-items');
        if ($forceItems) {
            $this->warn('Modo de sincronização COMPLETA (com itens) ativado.');
        } else {
            $this->info('Modo de sincronização RÁPIDA (apenas dados gerais do pedido) ativado.');
        }

        $page = 1;
        do {
            $this->line("Buscando página {$page} de pedidos...");
            $ordersFromApi = $tinyApiService->getOrders($page);
            if (empty($ordersFromApi)) break;

            foreach ($ordersFromApi as $orderSummary) {
                if ($forceItems) {
                    // LÓGICA COMPLETA: Busca detalhes e sincroniza itens
                    $this->line("Processando completo (com itens) o pedido Tiny ID: {$orderSummary['id']}");
                    $orderData = $tinyApiService->getSingleOrder($orderSummary['id']);
                    if (!$orderData) continue;

                    $localOrder = Order::updateOrCreate(
                        ['tiny_id' => $orderData['id']],
                        [
                            'customer_name' => $orderData['cliente']['nome'],
                            'order_date' => Carbon::createFromFormat('d/m/Y', $orderData['data_pedido'])->toDateString(),
                            'total_amount_cache' => $orderData['total_pedido'],
                        ]
                    );
                    $this->syncOrderItems($localOrder, $orderData);
                    sleep(1);

                } else {
                    // LÓGICA RÁPIDA: Usa apenas os dados da lista
                    $this->line("Processando rápido o pedido Tiny ID: {$orderSummary['id']}");
                    Order::updateOrCreate(
                        ['tiny_id' => $orderSummary['id']],
                        [
                            'customer_name' => $orderSummary['nome'],
                            'order_date' => Carbon::createFromFormat('d/m/Y', $orderSummary['data_pedido'])->toDateString(),
                            'total_amount_cache' => $orderSummary['valor'],
                        ]
                    );
                }
            }
            $page++;
        } while (true);

        $this->info("Sincronização de pedidos concluída!");
        return 0;
    }
}
