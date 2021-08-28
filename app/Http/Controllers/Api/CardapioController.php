<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cardapio;
use App\Models\Restaurante;
use DB;
use App\Traits\ResponseAPI;


class CardapioController extends Controller
{
    use ResponseAPI;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $cardapios = Cardapio::with('restaurante')->get();

            return $this->success(
                "Todos os cardápios", 
                200, 
                $cardapios
            );

        } catch(\Exception $e) {
            return $this->error(
                $e->getMessage(), 
                $e->getCode()
            );
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'descricao' => 'required',
                'ativo' => 'required'
            ]);

            DB::beginTransaction();

            $restaurante = Restaurante::find($request->restaurante_id);

            if(!$restaurante) 
                return $this->error(
                    'Restaurante não encontrado', 
                    400
                );

            if(!$this->isValidAssociateCardapioToRestaurante($restaurante))
                return $this->error(
                    "O restaurante {$restaurante->nome} já possui 3 cardápios ativos! 
                    Desative um cardápio existente para que seja possível cadastrar um novo", 
                    500
                );

            $cardapio = new Cardapio();
            $cardapio->descricao = $request->descricao;
            $cardapio->ativo = 1;
            $cardapio->restaurante()->associate($restaurante);
            $cardapio->save();

            DB::commit();

            return $this->success(
                "Cardápio criado com sucesso!", 
                201, 
                $cardapio
            );

        } catch(\Exception $e) {
            DB::rollBack();
            dd($e);
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
            $cardapio = Cardapio::find($id);
            
            return $this->success(
                "Cardápio", 
                200, 
                $cardapio
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
                'descricao' => 'required',
                'ativo' => 'required'
            ]);
            
            $restaurante = Restaurante::find($request->restaurante_id);
            if(!$restaurante) 
                return $this->error(
                    'Restaurante não encontrado', 
                    400
                );

            $cardapio = Cardapio::find($id);
            if(!$cardapio) 
                return $this->error(
                    'Cardápio não encontrado', 
                    400
                );

            $cardapio->descricao = $request->descricao;
            $cardapio->ativo = $request->ativo;
            $cardapio->restaurante()->associate($restaurante);
            $cardapio->save();

            DB::commit();

            return $this->success(
                "Cardápio alterado com sucesso!", 
                200, 
                $cardapio
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
            $cardapio = Cardapio::find($id);

            if(!$cardapio) 
                return $this->error(
                    'Cardápio não encontrado', 
                    400
                );

            $cardapio->delete();

            DB::commit();

            return $this->success(
                "Cardápio deletado com sucesso!", 
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
