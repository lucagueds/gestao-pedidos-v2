<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validação dos dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // 'unique:users' garante que o email não exista
            'password' => 'required|string|min:8|confirmed', // 'confirmed' procura por um campo 'password_confirmation'
        ]);

        // 2. Criação do usuário
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // SEMPRE armazene senhas com hash
        ]);

        // 3. Cria um token para o novo usuário (login automático)
        $token = $user->createToken('api-token')->plainTextToken;

        // 4. Retorna os dados do usuário e o token
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201); // 201 Created
    }

    public function login(Request $request)
    {
        // 1. Valida os dados de entrada
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Tenta autenticar o usuário
        if (!Auth::attempt($credentials)) {
            // Se a autenticação falhar, retorna um erro
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401); // 401 Unauthorized
        }

        // 3. Se a autenticação for bem-sucedida...
        $user = $request->user();

        // 4. Cria um novo token para o usuário
        $token = $user->createToken('api-token')->plainTextToken;

        // 5. Retorna os dados do usuário e o token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoga (invalida) o token que foi usado para fazer a requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}
