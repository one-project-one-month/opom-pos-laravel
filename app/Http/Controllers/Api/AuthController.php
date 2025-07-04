<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create access token
        $accessToken = $user->createToken('access_token')->plainTextToken;

        // Create refresh token (store in DB or cache, here as a user column for demo)
        $refreshToken                   = Str::random(64);
        $user->refresh_token            = $refreshToken;
        $user->refresh_token_expires_at = Carbon::now()->addDays(7);
        $user->save();

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    // Logout and revoke tokens
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $user->refresh_token            = null;
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
        if (! $user) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }
        // Issue new access token
        $accessToken = $user->createToken('access_token')->plainTextToken;
        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 60 * 60, // 1 hour
        ]);
    }

    // Get current user
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
