<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProdutoRequest;
use App\Models\Produto;
use App\Models\Cardapio;
use DB;
use App\Traits\ResponseAPI;
use App\Enum\HttpStatusCode;

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

            return $this->success(HttpStatusCode::OK, $produtos);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    private function isValidAssociateProdutoAoCardapio($cardapio)
    {
        return count($cardapio->produtos) == 10 ? FALSE : TRUE;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProdutoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProdutoRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $cardapio = Cardapio::find($request->cardapio_id);

            if(!$cardapio) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Cardápio não encontrado'
                );

            if(!$this->isValidAssociateProdutoAoCardapio($cardapio)){
                return $this->error(
                    HttpStatusCode::UNPROCESSABLE_ENTITY,
                    "O cardápio {$cardapio->descricao} já possui 10 produtos! 
                    O máximo é 10."
                );
            }

            $produto = new Produto();
            $produto->descricao = $request->descricao;
            $produto->cardapio()->associate($cardapio);
            $produto->save();

            DB::commit();

            return $this->success(HttpStatusCode::CREATED, $produto);

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
            $produto = Produto::find($id);
            
            return $this->success(HttpStatusCode::OK, $produto);

        } catch(\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());     
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProdutoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProdutoRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            
            $cardapio = Cardapio::find($request->cardapio_id);

            if(!$cardapio) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Cardápio não encontrado'
                );

            $produto = Produto::find($id);
            if(!$produto) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Produto não encontrado'
                );

            $produto->descricao = $request->descricao;
            $produto->cardapio()->associate($cardapio);
            $produto->save();

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
            $produto = Produto::find($id);

            if(!$produto) 
                return $this->error(
                    HttpStatusCode::NOT_FOUND,
                    'Produto não encontrado'
                );

            $produto->delete();

            DB::commit();

            return $this->success(HttpStatusCode::DELETED);
            
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getCode(), $e->getMessage());         
        }
    }
}
