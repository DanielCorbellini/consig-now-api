<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venda extends Model
{
    protected $fillable = [
        "cliente_id",
        "representante_id", // retirar depois
        "condicional_id",
        "data_venda",
        "valor_total",
        "forma_pagamento"
    ];

    // public function cliente(): BelongsTo
    // {
    //     return $this->belongsTo(Cliente::class);
    // }

    public function condicional(): BelongsTo
    {
        return $this->belongsTo(Condicional::class, 'condicional_id')->with('representante');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function representante(): BelongsTo
    {
        return $this->belongsTo(Representante::class, "representante_id")->with('user');
    }

    /**
     * Scope para filtros dinâmicos
     */
    #[Scope]
    protected function filtrar(Builder $query, array $filtros = []): Builder
    {
        return $query
            ->when($filtros['data_inicio'] ?? null, fn($q, $inicio) =>
            $q->where('data_venda', '>=', $inicio))
            ->when($filtros['data_fim'] ?? null, fn($q, $fim) =>
            $q->where('data_venda', '<=', $fim))
            ->when($filtros['cliente_id'] ?? null, fn($q, $id) =>
            $q->where('cliente_id', $id))
            ->when($filtros['condicional_id'] ?? null, fn($q, $id) =>
            $q->where('condicional_id', $id))
            ->when($filtros['forma_pagamento'] ?? null, fn($q, $forma) =>
            $q->where('forma_pagamento', $forma))
            ->when(
                $filtros['representante_id'] ?? null,
                fn($q, $id) =>
                $q->whereHas('condicional.representante', fn($q2) =>
                $q2->where('representantes.id', $id))
            );
    }
}
