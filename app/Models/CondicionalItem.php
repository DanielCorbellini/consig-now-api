<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondicionalItem extends Model
{
    protected $table = "condicional_itens";

    protected $fillable = [
        'condicional_id',
        'produto_id',
        'quantidade_entregue',
        'quantidade_devolvida',
        'quantidade_vendida',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->quantidade_devolvida = 0;
            $item->quantidade_vendida = 0;
        });
    }

    public function condicional(): BelongsTo
    {
        return $this->belongsTo(Condicional::class, 'id');
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
