<?php

namespace App\Services;

use Exception;
use App\Models\Estoque;
use App\Models\Almoxarifado;
use App\Models\Representante;
use Illuminate\Database\Eloquent\Collection;

class EstoqueService
{
    public function criar(array $data)
    {
        return Estoque::create($data);
    }

    public function listar(): Collection
    {
        return Estoque::all();
    }

    public function listarPorId(int $id)
    {
        return Estoque::find($id);
    }

    public function deletar(int $id): bool
    {
        return Estoque::destroy($id) > 0;
    }

    public function listarPorAlmoxarifado(int $almoxarifadoId): Collection
    {
        return Estoque::where('almoxarifado_id', $almoxarifadoId)
            ->with(['produto', 'produto.categoria'])
            ->get();
    }

    public function listarComProduto(array $filtros = []): ?Collection
    {
        $representante = Representante::where('user_id', $filtros['usuario_id'])->first();

        if (empty($representante)) {
            throw new Exception('Representante não encontrado');
        }

        $almoxarifado = Almoxarifado::where('representante_id', $representante->id)->first();
        if (empty($almoxarifado)) {
            throw new Exception('Almoxarifado não encontrado');
        }

        $query = Estoque::with(['produto', 'produto.categoria', 'almoxarifado']);
        $query->where('almoxarifado_id', $almoxarifado->id);

        return $query->get();
    }
}
