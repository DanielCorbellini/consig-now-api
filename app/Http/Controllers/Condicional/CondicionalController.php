<?php

namespace App\Http\Controllers\Condicional;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CondicionalService;
use App\Http\Requests\Condicional\CondicionalIndexRequest;
use App\Http\Requests\condicional\CondicionalItemStore;
use App\Http\Requests\Condicional\CondicionalStoreRequest;
use App\Http\Requests\Condicional\CondicionalUpdateRequest;

class CondicionalController extends Controller
{
    protected $condicionalService;

    public function __construct(CondicionalService $condicionalService)
    {
        $this->condicionalService = $condicionalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CondicionalIndexRequest $request)
    {
        $validatedConditionals = $request->validated();
        $condicionais = $this->condicionalService->listar($validatedConditionals);

        if ($condicionais->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Condicionais não encontradas'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'condicional' => $condicionais
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CondicionalStoreRequest $request)
    {
        $validatedConditionals = $request->validated();
        $condicional = $this->condicionalService->criar($validatedConditionals);

        if (!$condicional) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar condicional'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Condicional criada com sucesso',
            'condicional' => $condicional
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $condicionais = $this->condicionalService->listarPorId($id);

        if (empty($condicionais)) {
            return response()->json([
                'success' => false,
                'message' => 'Condicional não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'condicional' => $condicionais
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CondicionalUpdateRequest $request, int $id)
    {
        $validatedConditionals = $request->validated();
        $condicional = $this->condicionalService->editar($id, $validatedConditionals);

        if (!$condicional) {
            return response()->json([
                'success' => false,
                'message' => 'Condicional não encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'condicional' => $condicional
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $deleted = $this->condicionalService->deletar($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível deletar a condicional ou ela não existe'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Condicional deletada com sucesso!'
        ], 200);
    }

    /**
     * adicionar movimentação de estoque ao adicionar item baseado no quantidade_entregue
     * colocar valor padrão em quantidade_devolvida como 0
     */
    public function addItem(CondicionalItemStore $request, int $id)
    {
        try {
            $condicional = $this->condicionalService->adicionarItem($id, $request->validated());

            return response()->json([
                'message' => 'Item adicionado com sucesso!',
                'condicional' => $condicional
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function showItems(int $id)
    {
        $itensCondicional = $this->condicionalService->listarItensPorId($id);

        if (empty($itensCondicional)) {
            return response()->json([
                'success' => false,
                'message' => 'Condicional não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'condicional' => $itensCondicional
        ], 200);
    }

    public function returnItems(Request $request, int $id)
    {
        $rules = [
            'item_id' => 'required|integer|exists:condicional_itens,id',
            'quantidade' => 'required|integer|min:1',
        ];

        $message = [
            'item_id.required' => 'O campo item_id é obrigatório.',
            'item_id.integer' => 'O campo item_id deve ser um número inteiro.',
            'item_id.exists' => 'O item especificado não existe na condicional.',
            'quantidade.required' => 'O campo quantidade é obrigatório.',
            'quantidade.integer' => 'O campo quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade mínima para devolução é 1.',
        ];

        $request->validate($rules, $message);

        try {
            $condicional = $this->condicionalService->devolverItem(
                $id,
                $request->input('item_id'),
                $request->input('quantidade')
            );

            return response()->json([
                'message' => 'Item devolvido com sucesso!',
                'condicional' => $condicional
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function removeItem(int $id, int $itemId)
    {
        try {
            $condicional = $this->condicionalService->removerItem($id, $itemId);

            return response()->json([
                'message' => 'Item removido com sucesso!',
                'condicional' => $condicional
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateItem(Request $request, int $id, int $itemId)
    {
        try {
            $condicional = $this->condicionalService->editarItem($id, $itemId, $request->all());

            return response()->json([
                'message' => 'Item atualizado com sucesso!',
                'condicional' => $condicional
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
