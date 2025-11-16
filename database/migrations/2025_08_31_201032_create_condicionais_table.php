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
        Schema::create('condicionais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representante_id')->constrained('representantes');
            $table->date('data_entrega');
            $table->date('data_prevista_retorno')->nullable();
            $table->foreignId('almoxarifado_id')->nullable()->constrained('almoxarifados');
            $table->enum('status', ['aberta', 'finalizada', 'em_cobranca'])->default('aberta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('condicionais');
    }
};
