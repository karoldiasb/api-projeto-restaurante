<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurante;
use App\Models\User;
use DB;
use App\Traits\ResponseAPI;
use App\Enum\HttpStatusCode;
use App\Http\Requests\RestauranteRequest;

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
            
            return $this->success(HttpStatusCode::OK, $restaurantes);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\RestauranteRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RestauranteRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = User::find($request->user_id);

            if(!$user) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Usuário não encontrado'
                );

            $restaurante = new Restaurante();
            $restaurante->nome = $request->nome;   
            $restaurante->user()->associate($user);
            $restaurante->save();

            DB::commit();

            return $this->success(HttpStatusCode::CREATED, $restaurante);

        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error(
                $e->getCode(),
                $e->getMessage(), 
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
            
            return $this->success(HttpStatusCode::OK, $restaurante);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RestauranteRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RestauranteRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            $restaurante = Restaurante::find($id);

            if(!$restaurante) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Restaurante não encontrado'
                );
            
            $restaurante->nome = $request->nome;
            $restaurante->save();

            DB::commit();

            return $this->success(HttpStatusCode::UPDATED);
            
        } catch(\Exception $e) {
            return $this->error(
                $e->getCode(),
                $e->getMessage(), 
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
                    HttpStatusCode::NOT_FOUND,
                    'Restaurante não encontrado'
                );

            $restaurante->delete();

            DB::commit();

            return $this->success(HttpStatusCode::DELETED);
            
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getCode(), $e->getMessage());     
        }
    }
}
