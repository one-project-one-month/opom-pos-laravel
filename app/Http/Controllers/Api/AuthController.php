<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // Forgot password
    public function forgotPassword(Request $request, Response $response)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email', 'errors' => $validator->errors()], 422);
        }

        $user = User::whereEmail($request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found']);
        }

        // $token = Str::random(60);
        // $user->password_reset_token = $token;
        // $user->password_reset_token_expires_at = now()->addHours(2);
        // $user->save();
        // $response = Password::sendResetLink($request->only('email'));

        Mail::to('emails.reset-password', ['token' => $response], function ($message) use ($request) {
            $message->to($request->email);
        });

        // return $response == Password::RESET_LINK_SENT
        //     ? response()->json(['message' => 'Reset link sent to your email'], 200)
        //     : response()->json(['message' => 'Unable to send reset link'], 400);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
            'password_confirmation' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful.']);
        }
        return response()->json(['message' => 'Invalid token or email.'], 400);
    }

    // Check if authenticated
    public function checkAuth(Request $request)
    {
        return response()->json(['authenticated' => $request->user() ? true : false]);
    }

    //register an account
    public function register(Request $request)
    {
        //error messages
        $errorMessages = [
            'name.required'     => "Name is required",
            'name.max'          => "Name shouldn't be more than 15 words",
            'email.required'    => "Email is required",
            'password.required' => "Password is required",
            'password.min'      => "Password must be at least 6 words",
            'password.regex'    => "Password must contain one uppercase, one lowercase, one digit and one special character, ",
        ];

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|max:15',
            'email'                 => 'required|email|unique:users,email',
            'password'              => [
                'required',
                'string',
                'min:6',              // at least 6 characters in length
                'regex:/[a-z]/',      // at least one lowercase letter
                'regex:/[A-Z]/',      // at least one uppercase letter
                'regex:/[0-9]/',      // at least one digit
                'regex:/[@$!%*#?&]/', // a special character
                'confirmed',
            ],
            'password_confirmation' => 'required',
        ], $errorMessages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Fail to register an account',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //create token
        $access_token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $access_token,
        ]);
    }

    // Login and issue access & refresh tokens
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid credentials', 'errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request->email)->first();

        // $user = Auth::user();
        $user->tokens()->delete(); // Clear existing tokens for a fresh session

        // Define token expiration times
        $accessTokenExpiresAt = Carbon::now()->addDays(1);
        $refreshTokenExpiresAt = Carbon::now()->addDays(7);

        // Create access and refresh tokens
        $accessToken = $user->createToken('access_token', ['*'], $accessTokenExpiresAt)->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], $refreshTokenExpiresAt)->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'access_token_expires_at' => $accessTokenExpiresAt,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_at' => $refreshTokenExpiresAt,
            'token_type' => 'Bearer',
        ]);
    }

    // Logout and revoke tokens
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->save();
        return response()->json(['message' => 'Logged out']);
    }

    public function refreshToken(Request $request)
    {
        $currentRefreshToken = $request->bearerToken();
        $refreshToken = PersonalAccessToken::findToken($currentRefreshToken);

        if (!$refreshToken || !$refreshToken->can('refresh') || $refreshToken->expires_at->isPast()) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $refreshToken->tokenable;
        $refreshToken->delete();

        $accessTokenExpiresAt = Carbon::now()->addDays(1);
        $refreshTokenExpiresAt = Carbon::now()->addDays(7);

        $newAccessToken = $user->createToken('access_token', ['*'], $accessTokenExpiresAt)->plainTextToken;
        $newRefreshToken = $user->createToken('refresh_token', ['refresh'], $refreshTokenExpiresAt)->plainTextToken;

        return response()->json([
            'access_token' => $newAccessToken,
            'access_token_expires_at' => $accessTokenExpiresAt,
            'refresh_token' => $newRefreshToken,
            'refresh_token_expires_at' => $refreshTokenExpiresAt,
            'token_type' => 'Bearer',
        ]);
    }

    // Get current user
    public function user(Request $request)
    {
        $user = $request->user();
        $staffRole = $user->role;
        return response()->json([
            'User_detail' => $user,
            'Staff_role' => $staffRole
        ]);
    }
}
