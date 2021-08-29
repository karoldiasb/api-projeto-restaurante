<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('/restaurantes', App\Http\Controllers\Api\RestauranteController::class);
Route::resource('/cardapios', App\Http\Controllers\Api\CardapioController::class);
Route::resource('/produtos', App\Http\Controllers\Api\ProdutoController::class);

