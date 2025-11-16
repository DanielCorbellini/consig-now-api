<?php

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use App\Services\EstoqueService;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    protected $estoqueService;

    public function __construct(EstoqueService $estoqueService)
    {
        $this->estoqueService = $estoqueService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estoques = $this->estoqueService->listar();

        if ($estoques->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Estoques não encontrados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'stocks' => $estoques
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'almoxarifado_id' => 'required|integer|exists:almoxarifados,id',
            'produto_id' => 'required|integer|exists:produtos,id',
            'quantidade' => 'required|integer|min:0',
        ];

        $messages = [
            'almoxarifado_id.required' => 'O ID do almoxarifado é obrigatório.',
            'almoxarifado_id.exists' => 'O almoxarifado selecionado não existe.',
            'produto_id.required' => 'O ID do produto é obrigatório.',
            'produto_id.exists' => 'O produto selecionado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.min' => 'A quantidade deve ser no mínimo 0.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $estoque = $this->estoqueService->criar($validatedData);

        if (!$estoque) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar estoque'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estoque criado com sucesso',
            'stock' => $estoque
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $estoque = $this->estoqueService->listarPorId($id);

        if (empty($estoque)) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'stock' => $estoque
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $deleted = $this->estoqueService->deletar($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Estoque não encontrado ou não pôde ser deletado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Estoque deletado com sucesso'
        ], 200);
    }
}
