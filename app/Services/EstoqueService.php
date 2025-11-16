<?php

namespace App\Services;

use App\Models\Estoque;
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
}
