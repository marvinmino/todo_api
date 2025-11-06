<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Create a new AuthController instance.
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user account
     * 
     * This endpoint allows you to create a new user account. After successful registration, you'll receive a bearer token that you can use to authenticate subsequent requests.
     * 
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'User registered successfully', 201);
    }

    /**
     * Login with email and password
     * 
     * Authenticate with your email and password to receive a bearer token. Use this token in the Authorization header for all protected endpoints.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->error('Invalid credentials', 401);
        }

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    /**
     * Logout user and revoke token
     * 
     * Revokes the current authentication token. After logout, you'll need to login again to get a new token.
     * 
    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Get authenticated user information
     * 
     * Returns the currently authenticated user's information. This endpoint requires a valid bearer token.
     */
    public function user(): JsonResponse
    {
        $user = $this->authService->getUser(auth()->user());

        return $this->success(['user' => new UserResource($user)], 'User retrieved successfully');
    }
}
