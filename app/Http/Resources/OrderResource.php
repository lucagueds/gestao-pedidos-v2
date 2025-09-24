<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tiny_id' => $this->tiny_id,
            'customer_name' => $this->customer_name,
            // Formata a data para o padrão brasileiro
            'order_date' => $this->order_date->format('d/m/Y'),
            'total_amount' => $this->total_amount_cache,
            // Adiciona a contagem de itens, um exemplo do poder dos Resources
            'item_count' => $this->items->count(),
        ];
    }
}
