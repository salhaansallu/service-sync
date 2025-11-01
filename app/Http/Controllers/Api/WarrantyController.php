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
        if (empty($request->serialNumber) && empty($request->billNumber) && empty($request->phoneNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => 'Please enter at least one of Serial Number, Bill Number, or Phone Number.'
            ]);
        }

        $warranty = WarrantyRecord::query();


        if (!empty($request->serialNumber)) {
            $warranty->where('serial_number', $request->serialNumber);
        }

        if (!empty($request->billNumber)) {
            $warranty->where('bill_number', $request->billNumber);
        }

        if (!empty($request->phoneNumber)) {
            $warranty->where('phone_number', $request->phoneNumber);
        }

        $warranty = $warranty->first();

        if (!$warranty) {
            return response()->json([
                'success' => false,
                'message' => 'No warranty found with the provided details',
                'error' => 'WARRANTY_NOT_FOUND'
            ]);
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
