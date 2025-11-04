<?php

namespace App\Services;

use App\Models\Condicional;
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
            throw new \Exception("A condicional está finalizada, não aceitando mais itens.");
        }

        $condicional->itens()->create([
            'produto_id' => $itemData['produto_id'],
            'quantidade_entregue' => $itemData['quantidade_entregue'],
            'quantidade_devolvida' => $itemData['quantidade_devolvida'],
            'quantidade_vendida' => $itemData['quantidade_vendida'],
        ]);

        // Aqui botar a lógica para movimentação de estoque
        return $condicional->load('itens.produto');
    }
}
