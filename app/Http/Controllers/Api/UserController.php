<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Traits\ResponseAPI;
use DB;

class UserController extends Controller
{
    use ResponseAPI;

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
               'name' => 'required',
               'email' => 'required|string|email|max:255|unique:users,email',
               'password' => 'required'
            ]);

            DB::beginTransaction();

            $user = new User();
            $user->password = $request->password;
            $user->email = $request->email;
            $user->name = $request->name;
            $user->save();

            DB::commit();

            return $this->success(
                "Usuário criado com sucesso!", 
                201, 
                $user
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
}
