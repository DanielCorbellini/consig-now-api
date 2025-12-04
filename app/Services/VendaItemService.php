<?php

namespace App\Services;

use Exception;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\CondicionalItem;
use Illuminate\Database\Eloquent\Collection;

class VendaItemService
{
    public function adicionar(int $vendaId, array $itemData): ?VendaItem
    {
        $venda = Venda::find($vendaId);

        if (!$venda) {
            throw new Exception('Venda não encontrada.');
        }

        $condicionalItem = CondicionalItem::where('condicional_id', $venda->condicional_id)
            ->where('produto_id', $itemData['produto_id'])
            ->first();

        if (!$condicionalItem) {
            throw new Exception('Produto não pertence à condicional vinculada.');
        }

        // Calcular saldo disponível
        $saldoDisponivel = $condicionalItem->quantidade_entregue - $condicionalItem->quantidade_vendida;

        if ($itemData['quantidade'] > $saldoDisponivel) {
            throw new Exception("Quantidade solicitada ({$itemData['quantidade']}) excede o saldo disponível ($saldoDisponivel).");
        }

        // Aumenta o campo quantidade_vendida em CondicionalItens
        $condicionalItem->increment('quantidade_vendida', $itemData['quantidade']);
        $venda->increment('valor_total', $itemData['quantidade'] * $itemData['preco_unitario']);

        $itemData['venda_id'] = $vendaId;
        return VendaItem::create($itemData);
    }

    public function listar(int $vendaId): ?Collection
    {
        return VendaItem::where('venda_id', $vendaId)->with('produto')->get();
    }

    public function remover(int $vendaId, int $vendaItemId): bool
    {
        $venda = Venda::find($vendaId);

        if (!$venda) {
            throw new Exception('Venda não encontrada.');
        }

        $vendaItem = VendaItem::where('id', $vendaItemId)->first();

        if (!$vendaItem) {
            throw new Exception("Item não encontrado na venda venda informada.");
        }

        $condicionalItem = CondicionalItem::where('condicional_id', $venda->condicional_id)
            ->where('produto_id', $vendaItem->produto_id)
            ->first();

        // Reduz o campo quantidade_vendida em CondicionalItens
        $condicionalItem->decrement('quantidade_vendida', $vendaItem->quantidade);
        $venda->decrement('valor_total', $vendaItem->quantidade * $vendaItem->preco_unitario);

        if (!$condicionalItem) {
            throw new Exception('Produto não pertence à condicional vinculada.');
        }

        return VendaItem::where('id', $vendaItemId)
            ->where('venda_id', $vendaId)->delete() > 0;
    }
}
