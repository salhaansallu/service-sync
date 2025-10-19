<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarrantyRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarrantyController extends Controller
{
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serialNumber' => 'required|string',
            'billNumber' => 'required|string',
            'phoneNumber' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $warranty = WarrantyRecord::where('serial_number', $request->serialNumber)
            ->where('bill_number', $request->billNumber)
            ->where('phone_number', $request->phoneNumber)
            ->first();

        if (!$warranty) {
            return response()->json([
                'success' => false,
                'message' => 'No warranty found with the provided details',
                'error' => 'WARRANTY_NOT_FOUND'
            ], 404);
        }

        $isValid = $warranty->isValid();

        if ($isValid) {
            return response()->json([
                'success' => true,
                'data' => [
                    'isValid' => true,
                    'product' => $warranty->product_name,
                    'purchaseDate' => $warranty->purchase_date->format('Y-m-d'),
                    'expiryDate' => $warranty->expiry_date->format('Y-m-d'),
                    'daysRemaining' => $warranty->days_remaining,
                    'coverageType' => $warranty->coverage_type,
                    'notes' => $warranty->notes,
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'data' => [
                    'isValid' => false,
                    'product' => $warranty->product_name,
                    'purchaseDate' => $warranty->purchase_date->format('Y-m-d'),
                    'expiryDate' => $warranty->expiry_date->format('Y-m-d'),
                    'message' => 'Warranty has expired',
                ]
            ], 200);
        }
    }
}
