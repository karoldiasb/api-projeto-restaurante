<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cardapio extends Model
{
    use HasFactory;

    function restaurante(){
        return $this->belongsTo('App\Models\Restaurante');
    }
    function produtos(){
        return $this->hasMany('App\Models\Produto');
    }
}
