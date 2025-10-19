<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => 'user_' . $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'phoneVerified' => $user->phone_verified,
                'notificationPreferences' => $user->notification_preferences ?? [
                    'email' => true,
                    'push' => true,
                    'sms' => false,
                ],
                'createdAt' => $user->created_at->toIso8601String(),
                'updatedAt' => $user->updated_at->toIso8601String(),
            ]
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'notificationPreferences' => 'sometimes|array',
            'notificationPreferences.email' => 'sometimes|boolean',
            'notificationPreferences.push' => 'sometimes|boolean',
            'notificationPreferences.sms' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
            $user->phone_verified = false; // Reset verification if phone changed
        }

        if ($request->has('notificationPreferences')) {
            $user->notification_preferences = $request->notificationPreferences;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => 'user_' . $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'phoneVerified' => $user->phone_verified,
                'notificationPreferences' => $user->notification_preferences,
                'updatedAt' => $user->updated_at->toIso8601String(),
            ]
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $user = $request->user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'error' => 'INVALID_PASSWORD'
            ], 400);
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }
}
