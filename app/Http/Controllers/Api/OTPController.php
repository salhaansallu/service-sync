<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(5);

        // Invalidate previous OTPs for this phone
        OtpVerification::where('phone', $request->phone)
            ->where('is_verified', false)
            ->delete();

        // Create new OTP
        OtpVerification::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'is_verified' => false,
            'attempts' => 0,
        ]);

        // In production, send OTP via SMS service
        // For development, you can log it or return it in response
        \Log::info("OTP for {$request->phone}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'phone' => $request->phone,
                'expiresIn' => 300, // 5 minutes in seconds
            ],
            // Remove this in production
            'dev_otp' => $otp
        ], 200);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $otpRecord = OtpVerification::where('phone', $request->phone)
            ->where('is_verified', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'error' => 'INVALID_OTP'
            ], 400);
        }

        // Increment attempts
        $otpRecord->attempts++;
        $otpRecord->save();

        if (!$otpRecord->isValid($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'error' => 'INVALID_OTP'
            ], 400);
        }

        // Mark as verified
        $otpRecord->is_verified = true;
        $otpRecord->save();

        // Update user's phone verification status if user is authenticated
        if ($request->user()) {
            $user = $request->user();
            $user->phone_verified = true;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully',
                'data' => [
                    'phoneVerified' => true,
                ]
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
        ], 200);
    }
}
