<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    function cardapio(){
        return $this->belongsTo('App\Models\Cardapio');
    }
}
