<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimentacoesEstoque extends Model
{
    protected $table = "movimentacoes_estoque";

    protected $fillable = [
        "produto_id",
        "almox_origem_id",
        "almox_destino_id",
        "quantidade",
        "user_id",
        "condicional_id"
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, "produto_id");
    }

    public function almoxarifadoOrigem()
    {
        return $this->belongsTo(Almoxarifado::class, "almox_origem_id");
    }

    public function almoxarifadoDestino()
    {
        return $this->belongsTo(Almoxarifado::class, "almox_destino_id");
    }

    public function condicional()
    {
        return $this->belongsTo(Condicional::class, "condicional_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
