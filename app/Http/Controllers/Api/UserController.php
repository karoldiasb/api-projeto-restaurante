<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CardapioRequest;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Traits\ResponseAPI;
use DB;
use App\Enum\HttpStatusCode;


class UserController extends Controller
{
    use ResponseAPI;

    /**
    * Store a newly created resource in storage.
    *
    * @param  \App\Http\Requests\UserRequest  $request
    * @return \Illuminate\Http\Response
    */
   public function store(UserRequest $request)
   {
       try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = new User();
            $user->password = $request->password;
            $user->email = $request->email;
            $user->name = $request->name;
            $user->save();

            DB::commit();

            return $this->success(
                HttpStatusCode::CREATED, 
                $user
            );

       } catch(\Exception $e) {
           DB::rollBack();
           return $this->error(
                $e->getCode(),
               $e->getMessage(), 
               $e->errors()
           );
       }
   }
}
