<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\Representante;
use Illuminate\Support\Facades\Auth;

class VendasService
{
    public function criar(array $data): ?Venda
    {
        return Venda::create($data);
    }

    public function listar(array $filtros = [])
    {
        // If user is a representante, filter by their representante_id
        $user = Auth::user();
        if ($user && $user->perfil !== 'admin') {
            $representante = Representante::where('user_id', $user->id)->first();
            if ($representante) {
                $filtros['representante_id'] = $representante->id;
            }
        }

        $vendas = Venda::filtrar($filtros)->get();
        return $vendas->load('representante.user');
    }

    public function listarPorId(int $id)
    {
        return Venda::find($id)->load('representante.user');
    }

    public function listarItensPorId(int $id)
    {
        $venda = Venda::find($id);
        return $venda->itens;
    }

    public function editar(int $id, array $data): ?Venda
    {
        $venda = Venda::find($id);

        // Mudar
        if (!$venda)
            return null;

        $venda->update($data);

        //Ajustar
        return $venda->fresh(['condicional', 'condicional.representante', 'itens']);
    }

    public function deletar(int $id): bool
    {
        return Venda::destroy($id) > 0;
    }

    public function adicionarItem(int $condicionalId, array $itemData)
    {
        // $venda = Venda::findOrFail($condicionalId);

        // if ($venda->status === 'finalizada') {
        //     throw new \Exception("A condicional está finalizada, não aceitando mais itens.");
        // }

        // $venda->itens()->create([
        //     'produto_id' => $itemData['produto_id'],
        //     'quantidade_entregue' => $itemData['quantidade_entregue'],
        //     'quantidade_devolvida' => $itemData['quantidade_devolvida'],
        //     'quantidade_vendida' => $itemData['quantidade_vendida'],
        // ]);

        // // Aqui botar a lógica para movimentação de estoque
        // return $venda->load('itens.produto');
    }

    public function encerrarVenda(int $vendaId): ?Venda
    {
        $venda = Venda::find($vendaId);

        if (!$venda) {
            throw new \Exception("Venda não encontrada.");
        }

        $venda->status = 'paga';
        $venda->save();

        return $venda->fresh(['condicional', 'condicional.representante', 'itens']);
    }
}
