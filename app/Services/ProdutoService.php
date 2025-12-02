<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\CategoriasProduto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProdutoService
{
    private function baseSearchQuery(): Builder
    {
        return Produto::select('id', 'descricao', 'preco_custo', 'preco_venda', 'categoria_id')
            ->with('categoria:id,descricao');
    }

    public function criarProduto(array $data): ?Produto
    {
        return Produto::create($data);
    }

    public function listarProdutos(): Collection
    {
        return $this->baseSearchQuery()->get();
    }

    public function listarProduto(int $id): ?Produto
    {
        return $this->baseSearchQuery()->find($id);
    }

    public function deletarProduto(int $id): bool
    {
        return Produto::destroy($id) > 0;
    }

    public function editarProduto(int $id, array $data): ?Produto
    {
        $produto = Produto::find($id);

        if (!$produto)
            return null;

        $produto->update($data);

        return $this->baseSearchQuery()->find($id);
    }

    public function listarCategorias(): Collection
    {
        return CategoriasProduto::all();
    }
}
