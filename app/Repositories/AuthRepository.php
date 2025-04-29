<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\AuthResource;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $credentials)
    {
        try {
            if (!Auth::attempt($credentials)) {
                return ResponseHelper::jsonResponse(
                    false,
                    'Email atau password salah',
                    null,
                    401
                );
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseHelper::jsonResponse(
                true,
                'Login berhasil',
                [
                    'user' => new AuthResource($user),
                    'token' => $token
                ],
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Login gagal',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    public function me()
    {
        try {
            $user = Auth::user();

            return ResponseHelper::jsonResponse(
                true,
                'Data user berhasil diambil',
                new AuthResource($user),
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Gagal mengambil data user',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    public function logout()
    {
        try {
            Auth::user()->currentAccessToken()->delete();

            return ResponseHelper::jsonResponse(
                true,
                'Logout berhasil',
                null,
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Logout gagal',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}