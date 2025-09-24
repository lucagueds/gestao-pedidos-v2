<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiny_id',
        'customer_name',
        'order_date',
        'total_amount_cache',
        'pdf_status',
        'pdf_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_date' => 'date', // Adicione esta linha
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'tiny_id';
    }

    /**
     * Um Pedido é composto por muitos Itens de Pedido.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relacionamento mais direto para pegar os Produtos de um Pedido.
     * Eloquent usará a tabela 'order_items' como tabela pivot.
     * com withPivot() trazemos as colunas extras da tabela pivot.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price_at_time_of_order');
    }
}
