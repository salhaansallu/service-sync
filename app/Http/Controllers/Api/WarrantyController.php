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
        if (!$request->has('pos_key') || sanitize($request->input('pos_key')) != env('WEBSITE_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
            ]);
        }

        if (empty($request->serial_number) && empty($request->bill_number) && empty($request->phone_number)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => 'Please enter at least one of Serial Number, Bill Number, or Phone Number.'
            ]);
        }

        $warranty = WarrantyRecord::query();


        if (!empty($request->serial_number)) {
            $warranty->where('serial_number', $request->serial_number);
        } elseif (!empty($request->bill_number)) {
            $warranty->where('bill_number', $request->bill_number);
        } elseif (!empty($request->phone_number)) {
            $warranty->where('phone_number', $request->phone_number);
        }

        $warranty = $warranty->get(['serial_number', 'bill_number', 'product_name', 'purchase_date', 'expiry_date'])->toArray();

        if (!$warranty) {
            return response()->json([
                'success' => false,
                'message' => 'No warranty found with the provided details',
                'error' => 'WARRANTY_NOT_FOUND'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $warranty
        ], 200);
    }
}
