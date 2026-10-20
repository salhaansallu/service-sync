<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:api_users,phone',
            'password' => 'required|string|min:8',
            'email' => 'sometimes|nullable|email|max:255|unique:api_users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        // Check if phone already exists
        if (ApiUser::where('phone', $request->phone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number already registered',
                'error' => 'DUPLICATE_PHONE'
            ]);
        }

        // Check if customer exists with this phone number in customers table
        $customer = \App\Models\customers::where('phone', $request->phone)->first();

        $user = ApiUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'phone_verified' => false,
            'customer_id' => $customer ? $customer->id : null,
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => false,
            ],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => [
                    'id' => 'user_' . $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phoneVerified' => $user->phone_verified,
                    'hasExistingCustomer' => $customer ? true : false,
                    'customerId' => $customer ? $customer->id : null,
                    'createdAt' => $user->created_at->toIso8601String(),
                ],
                'token' => $token,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        $user = ApiUser::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or password',
                'error' => 'INVALID_CREDENTIALS'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => 'user_' . $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'phoneVerified' => $user->phone_verified,
                    'hasExistingCustomer' => $user->customer_id ? true : false,
                    'customerId' => $user->customer_id,
                    'notificationPreferences' => $user->notification_preferences ?? [
                        'email' => true,
                        'push' => true,
                        'sms' => false,
                    ],
                ],
                'token' => $token,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:api_users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        $users = ApiUser::where('email', $request->email)->limit(2)->get();
        if ($users->count() !== 1) {
            return response()->json(['success' => false, 'message' => 'This email cannot be reset automatically.'], 409);
        }
        $user = $users->first();
        $broker = Password::broker('api_users');
        if ($broker->getRepository()->recentlyCreatedToken($user)) {
            return response()->json(['success' => false, 'message' => 'Please wait before requesting another reset.'], 429);
        }
        $token = $broker->createToken($user);
        Mail::raw(
            "Your password reset token is: {$token}\n\nSubmit it with your email and newPassword to POST /api/auth/reset-password. The token expires in 60 minutes.",
            function ($message) use ($user) {
                $message->to($user->email)->subject('WeFix password reset');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent successfully'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'newPassword' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        $status = Password::broker('api_users')->reset(
            [
                'email' => $request->email,
                'token' => $request->token,
                'password' => $request->newPassword,
                'password_confirmation' => $request->newPassword,
            ],
            function (ApiUser $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token',
                'error' => 'INVALID_RESET_TOKEN',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ], 200);
    }
}
