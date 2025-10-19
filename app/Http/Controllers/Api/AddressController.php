<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'addresses' => $addresses->map(function($address) {
                    return [
                        'id' => 'address_' . $address->id,
                        'userId' => 'user_' . $address->user_id,
                        'label' => $address->label,
                        'address' => $address->address,
                        'isDefault' => $address->is_default,
                        'createdAt' => $address->created_at->toIso8601String(),
                    ];
                })
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:50',
            'address' => 'required|string',
            'isDefault' => 'sometimes|boolean',
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

        // If setting as default, unset other defaults
        if ($request->isDefault) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $user->id,
            'label' => $request->label,
            'address' => $request->address,
            'is_default' => $request->isDefault ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => [
                'id' => 'address_' . $address->id,
                'userId' => 'user_' . $address->user_id,
                'label' => $address->label,
                'address' => $address->address,
                'isDefault' => $address->is_default,
                'createdAt' => $address->created_at->toIso8601String(),
            ]
        ], 201);
    }

    public function update(Request $request, $addressId)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'sometimes|string|max:50',
            'address' => 'sometimes|string',
            'isDefault' => 'sometimes|boolean',
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
        $address = Address::where('id', $addressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => 'ADDRESS_NOT_FOUND'
            ], 404);
        }

        if ($request->has('label')) {
            $address->label = $request->label;
        }

        if ($request->has('address')) {
            $address->address = $request->address;
        }

        if ($request->has('isDefault') && $request->isDefault) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $addressId)
                ->update(['is_default' => false]);
            $address->is_default = true;
        } elseif ($request->has('isDefault')) {
            $address->is_default = false;
        }

        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => [
                'id' => 'address_' . $address->id,
                'label' => $address->label,
                'address' => $address->address,
                'isDefault' => $address->is_default,
                'updatedAt' => $address->updated_at->toIso8601String(),
            ]
        ], 200);
    }

    public function destroy(Request $request, $addressId)
    {
        $user = $request->user();
        $address = Address::where('id', $addressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => 'ADDRESS_NOT_FOUND'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ], 200);
    }

    public function setDefault(Request $request, $addressId)
    {
        $user = $request->user();
        $address = Address::where('id', $addressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
                'error' => 'ADDRESS_NOT_FOUND'
            ], 404);
        }

        // Unset other defaults
        Address::where('user_id', $user->id)
            ->where('id', '!=', $addressId)
            ->update(['is_default' => false]);

        $address->is_default = true;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => 'Default address updated',
            'data' => [
                'id' => 'address_' . $address->id,
                'isDefault' => true,
            ]
        ], 200);
    }
}
