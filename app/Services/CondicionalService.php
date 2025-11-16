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
        // Colocar lógica de movimentação de estoque (Saída)
        // Pesquisar almox do representante
        // Sairá do almox centrar e irá para o do representante
        // Na devolução sairá do representante e irá para o central novamente
        return Condicional::create($data);
    }
    // public function devolver() --> usar o editar?

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
            throw new \Exception("A condicional está finalizada, não aceitando mais itens.");
        }

        $jaExiste = $condicional->itens()->where('produto_id', $itemData['produto_id'])->exists();

        if ($jaExiste) {
            throw new \Exception("Este produto já foi adicionado na condicional");
        }

        return DB::transaction(
            function () use ($condicional, $itemData) {

                $item = $condicional->itens()->create([
                    'produto_id' => $itemData['produto_id'],
                    'quantidade_entregue' => $itemData['quantidade_entregue'],
                    'quantidade_devolvida' => $itemData['quantidade_devolvida'] ?? 0,
                    'quantidade_vendida' => $itemData['quantidade_vendida'] ?? 0,
                ]);

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
            throw new \Exception("A quantidade deve ser maior que zero.");
        }

        if ($saldoDisponivel <= 0) {
            throw new \Exception("Não há mais itens disponíveis para devolver.");
        }

        if ($quantidade > $saldoDisponivel) {
            throw new \Exception("A devolução excede o saldo disponível ({$saldoDisponivel}).");
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
                ($item->quantidade_vendida + $item->quantidade_devolvida);
        });

        if ($todosResolvidos) {
            $condicional->update([
                'status' => 'finalizada',
            ]);
        }
    }
}
