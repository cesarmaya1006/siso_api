<?php

namespace App\Http\Controllers;

use App\Models\Admin\Usuario;
use App\Models\Personas\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!($token = auth()->attempt($credentials))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function persona($id)
    {
        $persona = Persona::with('tipos_docu')
            ->with('usuario')
            ->with('usuario.roles')
            ->with('usuario.roles.carnets')
            ->with('cargo')
            ->with('cargo.area')
            ->with('carrera')
            ->with('carrera.facultad')
            ->with('prestamos')
            ->findOrFail($id);
        return response()->json($persona);
    }
    public function prestamos($id)
    {
        $persona = Persona::with('tipos_docu')
            ->with('usuario')
            ->with('usuario.roles')
            ->with('usuario.roles.carnets')
            ->with('cargo')
            ->with('cargo.area')
            ->with('carrera')
            ->with('carrera.facultad')
            ->with('prestamos')
            ->with('prestamos.usuario')
            ->with('prestamos.producto')
            ->with('prestamos.producto')
            ->findOrFail($id);
        return response()->json($persona);
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

        return response()->json(['message' => 'Successfully logged out']);
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
    protected function respondWithToken($token)
    {
        //$usuario = Usuario::findOrFail(auth()->user('id'));
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth()
                    ->factory()
                    ->getTTL() * 60,
            'user' => auth()->user(),
            //'usuario' => $usuario
        ]);
    }
}
