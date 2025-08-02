<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            $result = $this->authService->login($credentials);

            if ($result) {
                return $this->apiResponse(
                    [
                        'user' => new UserResource($result['user']),
                        'token' => $result['token'],
                        'token_type' => 'Bearer'
                    ],
                    'Login successful',
                    200,
                    true
                );
            }

            return $this->apiResponseError(
                null,
                'Invalid credentials',
                401
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * User registration
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->authService->register($validatedData);

            return $this->apiResponse(
                [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => 'Bearer'
                ],
                'Registration successful',
                201,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        try {
            $user = $this->authService->me($request);

            return $this->apiResponse(
                new UserResource($user),
                'User retrieved successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request);

            return $this->apiResponse(
                null,
                'Logout successful',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }
}
