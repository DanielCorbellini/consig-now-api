<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almoxarifado extends Model
{
    protected $fillable = [
        "descricao",
        "tipo",
        "representante_id"
    ];

    public function representantes()
    {
        return $this->belongsTo(Representante::class, "representante_id");
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($almox) {
    //         $subscription->status = true;
    //         $subscription->data_inscricao = now();
    //     });
    // }

}
