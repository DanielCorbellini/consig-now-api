<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    protected $fillable = [
        "almoxarifado_id",
        "produto_id",
        "quantidade"
    ];

    public function almoxarifado()
    {
        return $this->belongsTo(Almoxarifado::class, "almoxarifado_id");
    }
}
