<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'list_name',
        'value',
    ];

    /**
     * Um Preço pertence a um Produto.
     * Note o nome do método no singular: 'product'.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
