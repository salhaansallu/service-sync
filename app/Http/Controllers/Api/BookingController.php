<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tvBrand' => 'required|string|max:100',
            'tvModel' => 'required|string|max:100',
            'issueType' => 'required|string|max:100',
            'issueDescription' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'pickupOption' => 'required|in:pickup,drop-off',
            'customerName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        $user = $request->user();

        $booking = Booking::create([
            'booking_id' => 'booking_' . time() . Str::random(6),
            'user_id' => $user->id,
            'customer_name' => $request->customerName,
            'customer_phone' => $request->phone,
            'tv_brand' => $request->tvBrand,
            'tv_model' => $request->tvModel,
            'issue_type' => $request->issueType,
            'issue_description' => $request->issueDescription,
            'address' => $request->address,
            'pickup_option' => $request->pickupOption,
            'status' => 'pending',
            'timeline' => [[
                'status' => 'pending',
                'timestamp' => now()->toIso8601String(),
                'note' => 'Booking created'
            ]],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $this->formatBookingResponse($booking)
        ], 201);
    }

    public function getUserBookings(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = Booking::where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings->items(),
                'pagination' => [
                    'total' => $bookings->total(),
                    'page' => $bookings->currentPage(),
                    'limit' => $bookings->perPage(),
                    'totalPages' => $bookings->lastPage(),
                ]
            ]
        ], 200);
    }

    public function getBooking(Request $request, $bookingId)
    {
        $user = $request->user();
        $booking = Booking::where('booking_id', $bookingId)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'error' => 'BOOKING_NOT_FOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatBookingResponse($booking, true)
        ], 200);
    }

    public function cancelBooking(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        $user = $request->user();
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'error' => 'BOOKING_NOT_FOUND'
            ], 404);
        }

        $booking->status = 'cancelled';
        $booking->addTimelineEntry('cancelled', $request->reason ?? 'Cancelled by customer');
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => [
                'id' => $booking->booking_id,
                'status' => $booking->status,
                'updatedAt' => $booking->updated_at->toIso8601String(),
            ]
        ], 200);
    }

    public function deleteBooking(Request $request, $bookingId)
    {
        $user = $request->user();
        $booking = Booking::where('booking_id', $bookingId)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'error' => 'BOOKING_NOT_FOUND'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ], 200);
    }

    private function formatBookingResponse($booking, $detailed = false)
    {
        $data = [
            'id' => $booking->booking_id,
            'userId' => 'user_' . $booking->user_id,
            'customerName' => $booking->customer_name,
            'customerPhone' => $booking->customer_phone,
            'tvBrand' => $booking->tv_brand,
            'tvModel' => $booking->tv_model,
            'issueType' => $booking->issue_type,
            'status' => $booking->status,
            'createdAt' => $booking->created_at->toIso8601String(),
            'updatedAt' => $booking->updated_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['issueDescription'] = $booking->issue_description;
            $data['address'] = $booking->address;
            $data['pickupOption'] = $booking->pickup_option;
            $data['timeline'] = $booking->timeline;
        }

        return $data;
    }
}
