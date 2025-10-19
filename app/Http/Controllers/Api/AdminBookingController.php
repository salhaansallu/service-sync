<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $query = Booking::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $bookings = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        // Calculate stats
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'inProgress' => Booking::where('status', 'in-progress')->count(),
            'testing' => Booking::where('status', 'testing')->count(),
            'ready' => Booking::where('status', 'ready')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings->map(function($booking) {
                    return [
                        'id' => $booking->booking_id,
                        'userId' => 'user_' . $booking->user_id,
                        'customerName' => $booking->customer_name,
                        'customerPhone' => $booking->customer_phone,
                        'tvBrand' => $booking->tv_brand,
                        'tvModel' => $booking->tv_model,
                        'status' => $booking->status,
                        'createdAt' => $booking->created_at->toIso8601String(),
                    ];
                }),
                'stats' => $stats,
                'pagination' => [
                    'total' => $bookings->total(),
                    'page' => $bookings->currentPage(),
                    'limit' => $bookings->perPage(),
                    'totalPages' => $bookings->lastPage(),
                ]
            ]
        ], 200);
    }

    public function updateStatus(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,parts-ordered,in-progress,testing,ready,completed,cancelled',
            'note' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $booking = Booking::where('booking_id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
                'error' => 'BOOKING_NOT_FOUND'
            ], 404);
        }

        $booking->status = $request->status;
        $booking->addTimelineEntry($request->status, $request->note);
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => [
                'id' => $booking->booking_id,
                'status' => $booking->status,
                'timeline' => $booking->timeline,
                'updatedAt' => $booking->updated_at->toIso8601String(),
            ]
        ], 200);
    }
}
