<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Realiza a autenticação",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"} ,
     *             @OA\Property(property="email", type="string", example="test1@example.com"),
     *             @OA\Property(property="password", type="string", example="teste")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Realizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example="3600"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Credenciais Inválidas",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Credenciais Inválidas"),
     *            @OA\Property(property="status", type="integer", example="401"),
     *        )
     *     )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciais Inválidas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/user/me",
     *     summary="Retorna as informações do usuário",
     *     tags={"Usuário"},
     *     @OA\Response(
     *         response=200,
     *         description="Informações do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="name", type="string", example="Test User"),
     *             @OA\Property(property="email", type="string", example="test1@example.com"),
     *             @OA\Property(property="email_verified_at", type="datetime", example="2025-03-10T21:43:41.000000Z"),
     *             @OA\Property(property="created_at", type="datetime", example="2025-03-10T21:43:42.000000Z"),
     *             @OA\Property(property="updated_at", type="datetime", example="2025-03-10T21:43:42.000000Z"),
     *         )
     *     )
     * )
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Encerra a sessão",
     *     tags={"Autenticação"},
     *     @OA\Response(
     *         response=200,
     *         description="Encerra a sessão",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="success", example="success"),
     *             @OA\Property(property="message", type="string", example="Saiu com sucesso"),
     *         )
     *     ),
     * )
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Saiu com sucesso'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
