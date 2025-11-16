<?php

namespace App\Http\Controllers\Almoxarifado;

use App\Http\Controllers\Controller;
use App\Services\AlmoxarifadoService;
use Illuminate\Http\Request;

class AlmoxarifadoController extends Controller
{
    protected $almoxarifadoService;

    public function __construct(AlmoxarifadoService $almoxarifadoService)
    {
        $this->almoxarifadoService = $almoxarifadoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $almoxarifados = $this->almoxarifadoService->listar();

        if ($almoxarifados->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Almoxarifados não encontrados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'warehouses' => $almoxarifados
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'descricao' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'representante_id' => 'nullable|integer|exists:representantes,id',
        ];

        $messages = [
            'descricao.required' => 'A descrição é obrigatória.',
            'tipo.required' => 'O tipo é obrigatório.',
            'representante_id.exists' => 'O representante selecionado não existe.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $almoxarifado = $this->almoxarifadoService->criar($validatedData);

        if (!$almoxarifado) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar almoxarifado'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Almoxarifado criado com sucesso',
            'warehouse' => $almoxarifado
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $almoxarifado = $this->almoxarifadoService->listarPorId($id);

        if (empty($almoxarifado)) {
            return response()->json([
                'success' => false,
                'message' => 'Almoxarifado não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'warehouse' => $almoxarifado
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
        $deleted = $this->almoxarifadoService->deletar($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar almoxarifado ou almoxarifado não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Almoxarifado deletado com sucesso'
        ], 200);
    }
}
