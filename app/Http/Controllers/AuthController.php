<?php

namespace App\Http\Controllers;

use App\Http\Requests\TokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store_token(TokenRequest $request): JsonResponse
    {
        $user = User::get_by_email($request->get('email'));

        if (!$user || !$user->validate_password($request->get('password'))) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('Personal Access Token');

        return response()->json([
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->token->expires_at,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->load(['roles', 'adminUniversities', 'adminCareers', 'adminSubjects']);
        return response()->json($user);
    }

}
