<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Cardapio;
use DB;
use App\Traits\ResponseAPI;


class ProdutoController extends Controller
{
    use ResponseAPI;

    /**
     * Create a new ProdutoController instance.
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
            $produtos = Produto::with('cardapio')->get();

            return $this->success(
                "Todos os produtos", 
                200, 
                $produtos
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'descricao' => 'required',
            ]);

            DB::beginTransaction();

            $cardapio = Cardapio::find($request->cardapio_id);

            if(!$cardapio) 
                return $this->error(
                    'Cardápio não encontrado', 
                    400
                );

            $produto = new Produto();
            $produto->descricao = $request->descricao;
            $produto->cardapio()->associate($cardapio);
            $produto->save();

            DB::commit();

            return $this->success(
                "Produto criado com sucesso!", 
                201, 
                $produto
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
            $produto = Produto::find($id);
            
            return $this->success(
                "Produto", 
                200, 
                $produto
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
            ]);
            
            $cardapio = Cardapio::find($request->cardapio_id);

            if(!$cardapio) 
                return $this->error(
                    'Cardápio não encontrado', 
                    400
                );

            $produto = Produto::find($id);
            if(!$produto) 
                return $this->error(
                    'Produto não encontrado', 
                    400
                );

            $produto->descricao = $request->descricao;
            $produto->cardapio()->associate($cardapio);
            $produto->save();

            DB::commit();

            return $this->success(
                "Produto alterado com sucesso!", 
                200, 
                $produto
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
            $produto = Produto::find($id);

            if(!$produto) 
                return $this->error(
                    'Produto não encontrado', 
                    400
                );

            $produto->delete();

            DB::commit();

            return $this->success(
                "Produto deletado com sucesso!", 
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
