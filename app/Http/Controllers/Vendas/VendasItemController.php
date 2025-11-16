<?php

namespace App\Http\Controllers\Vendas;

use Illuminate\Http\Request;
use App\Services\VendasService;
use App\Services\VendaItemService;
use App\Http\Controllers\Controller;
use Exception;

class VendasItemController extends Controller
{
    protected $vendaItemService;
    protected $vendaService;

    public function __construct(VendaItemService $vendaItemService, VendasService $vendaService)
    {
        $this->vendaItemService = $vendaItemService;
        $this->vendaService = $vendaService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $vendaId)
    {
        try {

            $rules = [
                'produto_id' => 'required|integer|exists:produtos,id',
                'quantidade' => 'required|integer|min:1',
                'preco_unitario' => 'required|numeric|min:0',
            ];

            $messages = [
                'produto_id.required' => 'O campo produto_id é obrigatório.',
                'produto_id.integer' => 'O campo produto_id deve ser um número inteiro.',
                'produto_id.exists' => 'O produto especificado não existe.',
                'quantidade.required' => 'O campo quantidade é obrigatório.',
                'quantidade.integer' => 'O campo quantidade deve ser um número inteiro.',
                'quantidade.min' => 'A quantidade mínima é 1.',
                'preco_unitario.required' => 'O campo preco_unitario é obrigatório.',
                'preco_unitario.numeric' => 'O campo preco_unitario deve ser um número.',
                'preco_unitario.min' => 'O preço unitário mínimo é 0.',
            ];

            $request->validate($rules, $messages);

            $venda = $this->vendaService->listarPorId($vendaId);

            if ($venda->status === 'paga') {
                return response()->json([
                    'success' => false,
                    'message' => 'A venda está finalizada, não aceitando mais itens.'
                ], 500);
            }

            $item = $this->vendaItemService->adicionar($vendaId, $request->all());

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao adicionar item à venda'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $vendaId)
    {
        $itens = $this->vendaItemService->listar($vendaId);

        if ($itens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Itens da venda não encontrados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $itens
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $vendaId, int $vendaItemId)
    {
        try {
            $removido = $this->vendaItemService->remover($vendaId, $vendaItemId);

            if (!$removido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao remover item da venda'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removido da venda com sucesso'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
