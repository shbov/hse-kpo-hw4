<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RoleRequest;
use App\Models\JwtSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = auth()->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken(string $token): JsonResponse
    {
        $user = auth()->user();
        $expiresAt = auth()->factory()->getTTL() * config('jwt.ttl');

        // В условии зачем-то просят таблицу с сессией, это вообще не безопасно,
        // но пусть будет, допустим, для дебага.
        //
        // Для проверки токенов используется отдельная библиотека,
        // так что с этим проблем нет
        JwtSession::create([
            'user_id' => $user->id,
            'session_token' => $token,
            'expires_at' => Carbon::now()->addSeconds($expiresAt)
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresAt,
            'user' => auth()->user()
        ]);
    }

    /**
     * Register a User.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create(array_merge(
            $request->all(),
            ['password' => Hash::make($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function userProfile(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Set a role for specific user
     *
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function setRole(RoleRequest $request): JsonResponse
    {
        if (!$user = User::find($request->user_id)) {
            return response()->json([
                'message' => "User is not found."
            ], 404);
        }

        if (!in_array($request->role, ['customer', 'chef', 'manager'])) {
            return response()->json([
                'message' => 'This role doesn\'t exists!'
            ], 400);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Role successfully updated.'
        ]);
    }
}
