<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tiny_id' => $this->tiny_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'items_per_box' => $this->items_per_box,
            // Gera a URL completa da imagem apenas se houver uma imagem salva
            'image_url' => $this->whenNotNull($this->image_path, asset('storage/' . $this->image_path)),
            // Inclui a lista de preços usando o PriceResource
            'prices' => PriceResource::collection($this->whenLoaded('prices')),
        ];
    }
}
