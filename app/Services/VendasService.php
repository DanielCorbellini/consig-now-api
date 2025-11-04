<?php

namespace App\Services;

use App\Models\Venda;
use Faker\Test\Provider\Collection;

class VendasService
{
    public function criar(array $data): ?Venda
    {
        return Venda::create($data);
    }

    public function listar(array $filtros = [])
    {
        return Venda::filtrar($filtros)->get();
    }

    public function listarPorId(int $id)
    {
        return Venda::find($id);
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
}
