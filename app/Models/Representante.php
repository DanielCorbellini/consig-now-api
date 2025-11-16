<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Retirar a coluna perfil de users
// Se estiver na tabela representantes, é um representante, se não um admin
class Representante extends Model
{
    protected $fillable = ['user_id'];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
