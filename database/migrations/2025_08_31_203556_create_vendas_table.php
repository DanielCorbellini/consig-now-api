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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representante_id')->constrained('representantes'); // retirar essa coluna dps
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('condicional_id')->nullable()->constrained('condicionais');
            $table->dateTime('data_venda');
            $table->decimal('valor_total', 10, 2);
            $table->enum('forma_pagamento', ['dinheiro', 'cartao', 'pix', 'outro']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
