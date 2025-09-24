<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

trait ManagesOrderItems
{
    private function syncOrderItems(Order $localOrder, array $orderData): void
    {
        // Primeiro, removemos os itens antigos para garantir consistência
        $localOrder->items()->delete();

        if (empty($orderData['itens'])) {
            return;
        }

        foreach ($orderData['itens'] as $itemWrapper) {
            $itemData = $itemWrapper['item'];

            $localProduct = Product::where('tiny_id', $itemData['id_produto'])->first();

            if (!$localProduct) {
                Log::warning("Produto com Tiny ID {$itemData['id_produto']} não encontrado durante a sincronização do pedido #{$localOrder->tiny_id}.");
                continue;
            }

            OrderItem::create([
                'order_id'                 => $localOrder->id,
                'product_id'               => $localProduct->id,
                'quantity'                 => $itemData['quantidade'],
                'price_at_time_of_order' => $itemData['valor_unitario'],
            ]);
        }
    }
}
