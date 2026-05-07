<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // TARUH KODENYA DI SINI
    public function getToken(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (! Auth::attempt($data)) {
                Log::info('[Auth - API] Email atau password salah');

                return response()->json([
                    'message' => 'Email atau password salah',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            
            // Membuat token menggunakan Laravel Sanctum
            $token = $user->createToken('api_token')->plainTextToken;
            
            Log::info($token);

            return response()->json([
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error saat login', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}