<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enum\HttpStatusCode;
use App\Traits\ResponseAPI;

class AuthController extends Controller
{
    use ResponseAPI;

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);   

        $user = User::where('email', '=', $request->email)->get()->first();
        if(is_null($user)){
            return $this->error(
                HttpStatusCode::NOT_FOUND,
                'Usuário não existe'
            );
        }
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->error(
                HttpStatusCode::NOT_FOUND,
                'Login inválido. Verifique suas credenciais.'
            );
        }
        
        return $this->respondWithTokenAndUserId($token, $user->id);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->success(
            HttpStatusCode::OK,
            "Usuário deslogado"
        );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithTokenAndUserId($token, $user_id)
    {
        return response()->json([
            'success' => true,
            'user_id' => $user_id,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], HttpStatusCode::OK);
    }
}
