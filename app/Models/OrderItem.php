<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_time_of_order',
    ];

    /**
     * Um Item de Pedido pertence a um Pedido.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Um Item de Pedido se refere a um Produto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
