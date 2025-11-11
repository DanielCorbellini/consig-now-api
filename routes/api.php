<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Vendas\VendasController;
use App\Http\Controllers\Produto\ProdutoController;
use App\Http\Controllers\Vendas\VendasItemController;
use App\Http\Controllers\Condicional\CondicionalController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// O middleware em questão verifica se o usuário possui o token após fazer login, para a sessão do usuário se manter
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix("produto")->group(
        function () {
            Route::post('/', [ProdutoController::class, 'store']);
            Route::get('/', [ProdutoController::class, 'index']);
            Route::get('/{id}', [ProdutoController::class, 'show']);
            Route::delete('/{id}', [ProdutoController::class, 'destroy']);
            Route::put('/{id}', [ProdutoController::class, 'update']);
        }
    );

    Route::prefix("condicional")->group(
        function () {
            Route::post('/', [CondicionalController::class, 'store']);
            Route::get('/', [CondicionalController::class, 'index']);
            Route::get('/{id}', [CondicionalController::class, 'show']);
            Route::delete('/{id}', [CondicionalController::class, 'destroy']);
            Route::put('/{id}', [CondicionalController::class, 'update']);

            // Itens da condicional
            Route::post('/{id}/itens/', [CondicionalController::class, 'addItem']);
            Route::get('/{id}/itens/', [CondicionalController::class, 'showItems']);
            Route::put('/{id}/itens/', [CondicionalController::class, 'returnItems']);
        }
    );

    Route::prefix("vendas")->group(
        function () {
            Route::post('/', [VendasController::class, 'store']);
            Route::get('/', [VendasController::class, 'index']);
            Route::get('/{id}', [VendasController::class, 'show']);
            Route::delete('/{id}', [VendasController::class, 'destroy']);
            Route::put('/{id}', [VendasController::class, 'update']);

            // Itens da venda
            Route::post('/{vendaId}/itens/', [VendasItemController::class, 'store']);
            Route::delete('/{vendaId}/itens/{vendaItemId}', [VendasItemController::class, 'destroy']);

            Route::get('/{vendaId}/itens/', [VendasItemController::class, 'show']);
        }
    );
});
