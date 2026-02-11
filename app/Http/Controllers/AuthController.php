<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Validar datos
    $validated = $request->validate([
        'username' => [
            'required',
            'string',
            'regex:/^[A-Za-z]+$/',
            'unique:users,username',
            'min:3',
            'max:20',
        ],
        'password' => [
            'required',
            'string',
            'min:6',
        ],
    ]);

    // Crear usuario
    $user = User::create([
        'username' => $validated['username'],
        'password' => Hash::make($validated['password']),
    ]);

    // 🔹 Crear token para el usuario
    $token = $user->createToken('api-token')->plainTextToken;

    // Responder con token
    return response()->json([
        'success' => true,
        'message' => 'Usuario registrado correctamente',
        'data' => [
            'token' => $token,
            'username' => $user->username
        ],
    ], 201);
}

    public function login(Request $request)
    {
        // 1. Validar credenciales
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Buscar usuario
        $user = User::where('username', $validated['username'])->first();

        // 3. Verificar credenciales
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas',
                'errors' => [],
            ], 401);
        }

        // 4. Crear token
        $token = $user->createToken('api-token')->plainTextToken;

        // 5. Responder con token
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'token' => $token,
                'username' => $user->username,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario autenticado
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
            'data' => null,
        ]);
    }
}
