<?php

namespace App\Services;

use Exception;
use App\Models\Estoque;
use App\Models\Condicional;
use App\Models\CondicionalItem;
use Illuminate\Support\Facades\DB;
use App\Models\MovimentacoesEstoque;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CondicionalService
{
    public function criar(array $data): ?Condicional
    {
        return Condicional::create($data);
    }

    public function listar(array $filtros = []): Collection
    {
        return Condicional::comRepresentante($filtros)->get();
    }

    public function listarPorId(int $id)
    {
        return Condicional::find($id);
    }

    public function listarItensPorId(int $id)
    {
        $condicional = Condicional::find($id);
        return $condicional->itens;
    }

    public function editar(int $id, array $data): ?Condicional
    {
        $condicional = Condicional::find($id);

        // Mudar
        if (!$condicional)
            return null;

        $condicional->update($data);

        //Ajustar
        return $condicional->fresh(['representante.user']);
    }

    public function deletar(int $id): bool
    {
        return Condicional::destroy($id) > 0;
    }

    public function adicionarItem(int $condicionalId, array $itemData)
    {
        $condicional = Condicional::findOrFail($condicionalId);

        if ($condicional->status === 'finalizada') {
            throw new Exception("A condicional está finalizada, não aceitando mais itens.");
        }

        $jaExiste = $condicional->itens()->where('produto_id', $itemData['produto_id'])->exists();

        if ($jaExiste) {
            throw new Exception("Este produto já foi adicionado na condicional");
        }

        return DB::transaction(
            function () use ($condicional, $itemData) {

                $item = $condicional->itens()->create([
                    'produto_id' => $itemData['produto_id'],
                    'quantidade_entregue' => $itemData['quantidade_entregue'],
                    'quantidade_devolvida' => $itemData['quantidade_devolvida'] ?? 0,
                    'quantidade_vendida' => $itemData['quantidade_vendida'] ?? 0,
                ]);

                $saldoDisponivel = Estoque::where('produto_id', $itemData['produto_id'])
                    ->where('almoxarifado_id', 1)
                    ->first()->quantidade;

                if ($saldoDisponivel < 0) {
                    throw new Exception("Não há saldo disponível para este produto.");
                }

                if ($item->quantidade_entregue > $saldoDisponivel) {
                    throw new Exception("Quantidade excede saldo disponível para este produto.");
                }

                MovimentacoesEstoque::create([
                    'produto_id' => $item->produto_id,
                    'almox_origem_id' => 1, // Central
                    'almox_destino_id' => $condicional->almoxarifado_id,
                    'quantidade' => $item->quantidade_entregue,
                    'user_id' => Auth::id(),
                    'condicional_id' => $condicional->id
                ]);

                // Atualiza o estoque central
                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', 1)
                    ->decrement('quantidade', $item->quantidade_entregue);

                // Atualiza o estoque do representante da condicional
                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', $condicional->almoxarifado_id)
                    ->increment('quantidade', $item->quantidade_entregue);

                return $condicional->load('itens.produto');
            }
        );
    }

    public function devolverItem(int $condicionalId, int $itemId, int $quantidade)
    {
        $condicional = Condicional::findOrFail($condicionalId);
        $item = CondicionalItem::where('condicional_id', $condicionalId)
            ->where('produto_id', $itemId)->firstOrFail();

        $saldoDisponivel = $item->quantidade_entregue
            - $item->quantidade_vendida
            - $item->quantidade_devolvida;

        if ($quantidade <= 0) {
            throw new Exception("A quantidade deve ser maior que zero.");
        }

        if ($saldoDisponivel <= 0) {
            throw new Exception("Não há mais itens disponíveis para devolver.");
        }

        if ($quantidade > $saldoDisponivel) {
            throw new Exception("A devolução excede o saldo disponível ({$saldoDisponivel}).");
        }

        return DB::transaction(
            function () use ($condicional, $item, $quantidade) {

                $condicional->itens()->where('produto_id', $item->produto_id)
                    ->increment('quantidade_devolvida', $quantidade);

                MovimentacoesEstoque::create([
                    'produto_id' => $item->produto_id,
                    'almox_origem_id' => $condicional->almoxarifado_id, // Central
                    'almox_destino_id' => 1,
                    'quantidade' => $quantidade,
                    'user_id' => Auth::id(),
                    'condicional_id' => $condicional->id
                ]);

                // Atualiza estoque representante
                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', $condicional->almoxarifado_id)
                    ->decrement('quantidade', $quantidade);

                // Atualiza estoque central
                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', 1)
                    ->increment('quantidade', $quantidade);

                $this->verificarEFinalizarCondicional($condicional);

                return $condicional->load('itens.produto');
            }
        );
    }

    private function verificarEFinalizarCondicional(Condicional $condicional)
    {
        // Recarregar itens atualizados
        $condicional->load('itens');

        // Verificar se TODOS os itens estão totalmente resolvidos
        $todosResolvidos = $condicional->itens->every(function ($item) {
            return $item->quantidade_entregue ==
                $item->quantidade_vendida + $item->quantidade_devolvida;
        });

        if ($todosResolvidos) {
            $condicional->update([
                'status' => 'finalizada',
            ]);
        }
    }
    public function removerItem(int $condicionalId, int $itemId)
    {
        $condicional = Condicional::findOrFail($condicionalId);
        $item = CondicionalItem::where('condicional_id', $condicionalId)
            ->where('id', $itemId)->firstOrFail();

        if ($condicional->status === 'finalizada') {
            throw new \Exception("A condicional está finalizada, não é possível remover itens.");
        }

        return DB::transaction(function () use ($condicional, $item) {
            $quantidadeTotal = $item->quantidade_entregue;

            // Atualiza estoque central (devolve tudo)
            Estoque::where('produto_id', $item->produto_id)
                ->where('almoxarifado_id', 1)
                ->increment('quantidade', $quantidadeTotal);

            Estoque::where('produto_id', $item->produto_id)
                ->where('almoxarifado_id', $condicional->almoxarifado_id)
                ->decrement('quantidade', $quantidadeTotal);

            MovimentacoesEstoque::create([
                'produto_id' => $item->produto_id,
                'almox_origem_id' => $condicional->almoxarifado_id,
                'almox_destino_id' => 1, // Central
                'quantidade' => $quantidadeTotal,
                'user_id' => Auth::id(),
                'condicional_id' => $condicional->id,
                'observacao' => 'Remoção de item da condicional'
            ]);

            $item->delete();

            return $condicional->load('itens.produto');
        });
    }

    public function editarItem(int $condicionalId, int $itemId, array $data)
    {
        $condicional = Condicional::findOrFail($condicionalId);
        $item = CondicionalItem::where('condicional_id', $condicionalId)
            ->where('id', $itemId)->firstOrFail();

        if ($condicional->status === 'finalizada') {
            throw new \Exception("A condicional está finalizada, não é possível editar itens.");
        }

        // Apenas permitindo editar a quantidade entregue por enquanto, pois é o que afeta o estoque inicial
        if (!isset($data['quantidade_entregue'])) {
            return $condicional->load('itens.produto');
        }

        $novaQuantidade = $data['quantidade_entregue'];
        $quantidadeAntiga = $item->quantidade_entregue;
        $diferenca = $novaQuantidade - $quantidadeAntiga;

        if ($diferenca == 0) {
            return $condicional->load('itens.produto');
        }

        return DB::transaction(function () use ($condicional, $item, $diferenca, $novaQuantidade) {

            if ($diferenca > 0) {
                $estoqueCentral = Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', 1)->first();

                if (!$estoqueCentral || $estoqueCentral->quantidade < $diferenca) {
                    throw new Exception("Estoque central insuficiente para adicionar mais itens.");
                }

                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', 1)
                    ->decrement('quantidade', $diferenca);

                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', $condicional->almoxarifado_id)
                    ->increment('quantidade', $diferenca);

                MovimentacoesEstoque::create([
                    'produto_id' => $item->produto_id,
                    'almox_origem_id' => 1,
                    'almox_destino_id' => $condicional->almoxarifado_id,
                    'quantidade' => $diferenca,
                    'user_id' => Auth::id(),
                    'condicional_id' => $condicional->id,
                    'observacao' => 'Ajuste (aumento) de item na condicional'
                ]);

            } else {
                $devolucao = abs($diferenca);

                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', $condicional->almoxarifado_id)
                    ->decrement('quantidade', $devolucao);

                Estoque::where('produto_id', $item->produto_id)
                    ->where('almoxarifado_id', 1)
                    ->increment('quantidade', $devolucao);

                MovimentacoesEstoque::create([
                    'produto_id' => $item->produto_id,
                    'almox_origem_id' => $condicional->almoxarifado_id,
                    'almox_destino_id' => 1,
                    'quantidade' => $devolucao,
                    'user_id' => Auth::id(),
                    'condicional_id' => $condicional->id,
                    'observacao' => 'Ajuste (redução) de item na condicional'
                ]);
            }

            $item->update(['quantidade_entregue' => $novaQuantidade]);

            return $condicional->load('itens.produto');
        });
    }
}
