<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'perfil' => 'required|in:admin,representante',
        ]);

        $user = $this->authService->register($validatedUser);

        return response()->json([
            "message" => "Usuário registrado com sucesso!",
            "user" => $user,
            "success" => true
        ], 201);
    }

    public function login(Request $request)
    {
        $validatedLogin = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $data = $this->authService->login(
            $validatedLogin['email'],
            $validatedLogin['password']
        );

        if (!$data['success']) {
            return response()->json([
                'message' => $data['message'],
                'success' => false,
            ], 401);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user' => $data['user'],
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'],
            'success' => true,
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        // Load representante with almoxarifado if user is a representante
        if ($user->perfil === 'representante') {
            $user->load('representante.almoxarifado');
        }

        return response()->json($user, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout efetuado com sucesso'], 200);
    }
}
