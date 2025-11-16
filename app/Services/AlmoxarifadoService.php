<?php

namespace App\Services;

use App\Models\Almoxarifado;
use Illuminate\Database\Eloquent\Collection;


class AlmoxarifadoService
{
    public function criar(array $data): Almoxarifado
    {
        return Almoxarifado::create($data);
    }

    public function listar(): Collection
    {
        return Almoxarifado::all();
    }

    public function listarPorId(int $id): ?Almoxarifado
    {
        return Almoxarifado::find($id);
    }

    public function deletar(int $id): bool
    {
        return Almoxarifado::destroy($id) > 0;
    }
}
