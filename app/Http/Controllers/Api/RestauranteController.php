<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurante;
use App\Models\User;
use DB;
use App\Traits\ResponseAPI;

class RestauranteController extends Controller
{
    use ResponseAPI;

     /**
     * Create a new RestauranteController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('apiJWT', ['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(!is_null(request('user_id'))){
                $restaurantes = Restaurante::with('cardapios', 'cardapios.produtos')
                    ->where('user_id', '=', request('user_id'))
                    ->get();
            }else{
                $restaurantes = Restaurante::with('cardapios', 'cardapios.produtos')->get();
            }
            
            return $this->success(
                "Todos os restaurantes", 
                200, 
                $restaurantes
            );

        } catch(\Exception $e) {
            return $this->error(
                $e->getMessage(), 
                $e->getCode()
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required',
            ]);

            DB::beginTransaction();

            $user = User::find($request->user_id);

            if(!$user) 
                return $this->error(
                    'Usuário não encontrado', 
                    400
                );

            $restaurante = new Restaurante();
            $restaurante->nome = $request->nome;   
            $restaurante->user()->associate($user);
            $restaurante->save();

            DB::commit();

            return $this->success(
                "Restaurante criado com sucesso!", 
                201, 
                $restaurante
            );

        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error(
                $e->getMessage(), 
                $e->getCode(),
                $e->errors()
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $restaurante = Restaurante::with('cardapios')->find($id);
            
            return $this->success(
                "Restaurante", 
                200, 
                $restaurante
            );

        } catch(\Exception $e) {
            return $this->error(
                $e->getMessage(), 
                $e->getCode(),
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required',
            ]);
            
            DB::beginTransaction();
            
            $restaurante = Restaurante::find($id);

            if(!$restaurante) 
                return $this->error(
                    'Restaurante não encontrado', 
                    400
                );
            
            $restaurante->nome = $request->nome;
            $restaurante->save();

            DB::commit();

            return $this->success(
                "Restaurante alterado com sucesso!", 
                200, 
                $restaurante
            );
            
        } catch(\Exception $e) {
            return $this->error(
                $e->getMessage(), 
                $e->getCode(),
                $e->errors()
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $restaurante = Restaurante::find($id);

            if(!$restaurante) 
                return $this->error(
                    'Restaurante não encontrado', 
                    400
                );

            $restaurante->delete();

            DB::commit();

            return $this->success(
                "Restaurante deletado com sucesso!", 
                200
            );
            
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error(
                $e->getMessage(), 
                $e->getCode()
            );     
        }
    }
}
