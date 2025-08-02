<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Attempt to log in a user with given credentials.
     *
     * @param array $credentials
     * @return array|null [User, token] on success, null on failure
     */
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token,
            ];
        }
        return null;
    }

    /**
     * Register a new user with validated data.
     *
     * @param array $validatedData
     * @return array [User, token]
     */
    public function register(array $validatedData)
    {
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'warehouse_id' => $validatedData['warehouse_id'],
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Get the authenticated user from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\User|null
     */
    public function me($request)
    {
        $user = $request->user();
        if ($user) {
            $user->load('warehouse');
        }
        return $user;
    }

    /**
     * Logout the authenticated user (delete current access token).
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function logout($request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}
