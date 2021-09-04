<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password'
    ];

    function user(){
        return $this->belongsTo('App\Models\User');
    }

    function cardapios(){
        return $this->hasMany('App\Models\Cardapio');
    }
}
