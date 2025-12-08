<?php

namespace App\Services;

use Exception;
use App\Models\Estoque;
use App\Models\Almoxarifado;
use App\Models\Representante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class EstoqueService
{
    public function criar(array $data)
    {
        return Estoque::create($data);
    }

    public function listar(): Collection
    {
        $user = Auth::user();

        // If user is a representante, filter by their almoxarifado
        if ($user && $user->perfil !== 'admin') {
            $representante = Representante::where('user_id', $user->id)->first();
            if ($representante) {
                $almoxarifado = Almoxarifado::where('representante_id', $representante->id)->first();
                if ($almoxarifado) {
                    return Estoque::where('almoxarifado_id', $almoxarifado->id)
                        ->with(['produto', 'produto.categoria', 'almoxarifado'])
                        ->get();
                }
            }
        }

        // Admin sees all
        return Estoque::with(['produto', 'produto.categoria', 'almoxarifado'])->get();
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
