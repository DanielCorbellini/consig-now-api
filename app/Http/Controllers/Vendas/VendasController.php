<?php

namespace App\Http\Controllers\Vendas;

use App\Services\VendasService;
use App\Http\Controllers\Controller;
use App\Services\CondicionalService;
use App\Http\Requests\Vendas\VendasIndexRequest;
use App\Http\Requests\Vendas\VendasStoreRequest;
use App\Http\Requests\Vendas\VendasUpdateRequest;
use Exception;

class VendasController extends Controller
{
    protected $vendasService;
    protected $condicionalService;

    public function __construct(VendasService $vendasService, CondicionalService $condicionalService)
    {
        $this->vendasService = $vendasService;
        $this->condicionalService = $condicionalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(VendasIndexRequest $request)
    {
        $validated = $request->validated();
        $vendas = $this->vendasService->listar($validated);

        if ($vendas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Vendas não encontradas'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vendas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendasStoreRequest $request)
    {
        $validatedSellings = $request->validated();

        $condicional = $this->condicionalService->listarPorId($validatedSellings['condicional_id']);

        if ($condicional->status === 'finalizada') {
            return response()->json([
                'success' => false,
                'message' => 'A condicional está finalizada.'
            ], 400);
        }

        $venda = $this->vendasService->criar($validatedSellings);

        if (!$venda) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar venda'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Venda criada com sucesso',
            'venda' => $venda
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $vendas = $this->vendasService->listarPorId($id);

        if (empty($vendas)) {
            return response()->json([
                'success' => false,
                'message' => 'Venda não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'vendas' => $vendas
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendasUpdateRequest $request, string $id)
    {
        $validatedSellings = $request->validated();
        $venda = $this->vendasService->editar($id, $validatedSellings);

        if (!$venda) {
            return response()->json([
                'success' => false,
                'message' => 'Venda não encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'venda' => $venda
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->vendasService->deletar($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível deletar a venda ou ela não existe'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Venda deletada com sucesso!'
        ], 200);
    }


    public function endSale(int $vendaId)
    {
        try {

            $finalizada = $this->vendasService->encerrarVenda($vendaId);

            if (!$finalizada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao finalizar a venda'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Venda finalizada com sucesso'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
