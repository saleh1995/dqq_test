<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * User login
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth-token')->plainTextToken;

                return $this->apiResponse(
                    [
                        'user' => new UserResource($user),
                        'token' => $token,
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

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'warehouse_id' => $validatedData['warehouse_id'],
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->apiResponse(
                [
                    'user' => new UserResource($user),
                    'token' => $token,
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
            $user = $request->user();

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
            $request->user()->currentAccessToken()->delete();

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
