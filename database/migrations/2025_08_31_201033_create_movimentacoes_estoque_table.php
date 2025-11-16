<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimentacoes_estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos');
            // Caso os almoxarifados forem null, será entendido como "central", ou seja, o produto voltou para a origem
            // Podemos verificar esta logica no backend
            $table->foreignId('almox_origem_id')->nullable()->constrained('almoxarifados');
            $table->foreignId('almox_destino_id')->nullable()->constrained('almoxarifados');
            // $table->enum('tipo', ['entrada', 'saida', 'transferencia', 'ajuste', 'devolucao', 'consignação']);
            $table->integer('quantidade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            // Ver esta coluna
            $table->foreignId('condicional_id')->constrained('condicionais');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_estoque');
    }
};
