<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Post(
        path: '/register',
        operationId: 'authRegister',
        tags: ['Auth'],
        summary: 'Inscription',
        description: 'Crée un utilisateur et renvoie un token Sanctum. Mot de passe haché automatiquement.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Test User'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'test@example.com'),
                    new OA\Property(property: 'password', type: 'string', minLength: 8, example: 'password123'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Inscription réussie',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Erreur de validation',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    #[OA\Post(
        path: '/login',
        operationId: 'authLogin',
        tags: ['Auth'],
        summary: 'Connexion',
        description: 'Authentifie un utilisateur et renvoie un token Sanctum. Limité à 10 requêtes par minute.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Connexion réussie',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Identifiants invalides ou validation échouée',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
            new OA\Response(response: 429, description: 'Trop de tentatives (throttle 10/min)'),
        ],
    )]
    public function login(LoginUserRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: '/logout',
        operationId: 'authLogout',
        tags: ['Auth'],
        summary: 'Déconnexion',
        description: 'Supprime le token Sanctum courant. Route protégée.',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Déconnexion réussie',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
            new OA\Response(response: 401, description: 'Non authentifié — token manquant ou invalide'),
        ],
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
