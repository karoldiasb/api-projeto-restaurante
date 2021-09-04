<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CardapioRequest;
use App\Models\Cardapio;
use App\Models\Restaurante;
use DB;
use App\Traits\ResponseAPI;
use App\Enum\HttpStatusCode;

class CardapioController extends Controller
{
    use ResponseAPI;

    /**
     * Create a new CardapioController instance.
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
                $cardapios = Cardapio::whereHas('restaurante', function ($query) {
                    $query->where('user_id', '=', request('user_id'));
                })->with('restaurante')->get();
            }else{
                $cardapios = Cardapio::with('restaurante')->get();
            }

            return $this->success(HttpStatusCode::OK, $cardapios);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    private function isValidAssociateCardapioToRestaurante($restaurante)
    {
        $count_cardapios_ativos = 0;
        foreach($restaurante->cardapios as $cardapio){
            if($cardapio->ativo == 1){
                $count_cardapios_ativos++;
            }
        }

        return $count_cardapios_ativos == 3 ? FALSE : TRUE;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CardapioRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardapioRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $restaurante = Restaurante::find($request->restaurante_id);

            if(!$restaurante) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Restaurante não encontrado', 
                );

            if(!$this->isValidAssociateCardapioToRestaurante($restaurante))
                return $this->error(
                    HttpStatusCode::UNPROCESSABLE_ENTITY,
                    "O restaurante {$restaurante->nome} já possui 3 cardápios ativos! 
                    Desative um cardápio existente para que seja possível cadastrar um novo"
                );

            $cardapio = new Cardapio();
            $cardapio->descricao = $request->descricao;
            $cardapio->ativo = 1;
            $cardapio->restaurante()->associate($restaurante);
            $cardapio->save();

            DB::commit();

            return $this->success(HttpStatusCode::CREATED, $cardapio);

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
            $cardapio = Cardapio::with('produtos')->find($id);
            
            return $this->success(HttpStatusCode::OK, $cardapio);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CardapioRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CardapioRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            $restaurante = Restaurante::find($request->restaurante_id);
            if(!$restaurante) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Restaurante não encontrado'
                );

            $cardapio = Cardapio::find($id);
            if(!$cardapio) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Cardápio não encontrado'
                );

            $cardapio->descricao = $request->descricao;
            $cardapio->ativo = $request->ativo;
            $cardapio->restaurante()->associate($restaurante);
            $cardapio->save();

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
            $cardapio = Cardapio::find($id);

            if(!$cardapio) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Cardápio não encontrado'
                );

            $cardapio->delete();

            DB::commit();

            return $this->success(HttpStatusCode::UPDATED);
            
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getCode(), $e->getMessage());        
        }
    }
}
