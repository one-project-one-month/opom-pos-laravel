<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    // Login and issue access & refresh tokens
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid credentials', 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create access token
        $accessToken = $user->createToken('access_token')->plainTextToken;

        // Create refresh token (store in DB or cache, here as a user column for demo)
        $refreshToken = Str::random(64);
        $user->refresh_token = $refreshToken;
        $user->refresh_token_expires_at = Carbon::now()->addDays(7);
        $user->save();

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    // Logout and revoke tokens
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $user->refresh_token = null;
        $user->refresh_token_expires_at = null;
        $user->save();
        return response()->json(['message' => 'Logged out']);
    }

    // Refresh access token using refresh token
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid refresh token', 'errors' => $validator->errors()], 422);
        }
        $user = User::where('refresh_token', $request->refresh_token)
            ->where('refresh_token_expires_at', '>', Carbon::now())
            ->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }
        // Issue new access token
        $accessToken = $user->createToken('access_token')->plainTextToken;
        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60, // 1 hour
        ]);
    }

    // Get current user
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
