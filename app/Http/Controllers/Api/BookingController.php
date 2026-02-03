<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // --------------------------------
        // Accept GET + POST data
        // --------------------------------
        $input = array_merge(
            $request->query(),              // GET parameters
            $request->request->all()         // POST parameters
        );

        // --------------------------------
        // POS key validation (API only)
        // --------------------------------
        if (
            request()->path() == 'api/bookings' &&
            (!isset($input['pos_key']) || sanitize($input['pos_key']) != env('WEBSITE_KEY'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error'   => 'VALIDATION_ERROR',
            ]);
        }

        // --------------------------------
        // Validation
        // --------------------------------
        $validator = Validator::make($input, [
            'address'          => 'required|string',
            'phone'            => 'required|string|max:20',
            'pickupOption'     => 'required|in:pickup,drop-off',
            'customerName'     => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error'   => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ]);
        }

        // --------------------------------
        // Generate Booking ID
        // --------------------------------
        $lastBooking = Booking::orderBy('id', 'DESC')->first(['booking_id']);
        $booking_id  = 'BOOK' . ($lastBooking
            ? (int) str_replace('BOOK', '', $lastBooking->booking_id) + 1
            : 1001
        );

        // --------------------------------
        // Create Booking
        // --------------------------------
        $booking = Booking::create([
            'booking_id'         => $booking_id,
            'customer_name'      => $input['customerName'],
            'customer_phone'     => formatOriginalPhoneNumber($input['phone']),
            'tv_brand'           => $input['tvBrand'],
            'tv_model'           => $input['tvModel'],
            'issue_type'         => $input['issueType'],
            'issue_description'  => $input['issueDescription'],
            'address'            => $input['address'],
            'pickup_option'      => $input['pickupOption'],
            'status'             => 'pending',
            'timeline'           => [[
                'status'    => 'pending',
                'timestamp' => now()->toIso8601String(),
                'note'      => 'Booking created'
            ]],
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Booking created successfully',
            'data'       => $this->formatBookingResponse($booking),
            'booking_id' => $booking_id,
        ], 200);
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

    public function n8n_get(Request $request)
    {
        // --------------------------------
        // Accept GET + POST inputs
        // --------------------------------
        $input = array_merge(
            $request->query(),          // GET parameters
            $request->request->all()    // POST parameters
        );

        $term   = isset($input['term']) ? sanitize($input['term']) : null;
        $status = isset($input['status']) ? sanitize($input['status']) : null;

        // --------------------------------
        // Base query
        // --------------------------------
        $qry = DB::table('bookings');

        // --------------------------------
        // Search by booking ID or phone
        // --------------------------------
        if (!empty($term)) {
            $qry->where(function ($query) use ($term) {
                $query->where('booking_id', '=', $term)
                    ->orWhere('customer_phone', '=', formatOriginalPhoneNumber($term));
            });
        }

        // --------------------------------
        // Filter by status
        // --------------------------------
        if (!empty($status)) {
            $qry->where('status', '=', $status);
        }

        // --------------------------------
        // Fetch data
        // --------------------------------
        $results = $qry->get([
            'booking_id',
            'customer_name',
            'customer_phone',
            'tv_brand',
            'tv_model',
            'issue_type',
            'issue_description',
            'pickup_option',
            'status',
            'estimated_cost',
            'final_cost',
            'created_at',
        ]);

        // --------------------------------
        // Response
        // --------------------------------
        if ($results->count() > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Bookings fetched successfully',
                'data'    => $results->toArray(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No bookings found',
            'data'    => [],
        ]);
    }

    public function listBookings() {
        if (isCashier()) {
            $bookings = Booking::query();

            if (isset($_GET['status'])) {
                $status = trim(sanitize($_GET['status']));

                $statusLower = strtolower($status);
                $ignoreStatuses = ['', 'all', '0', 'null', 'undefined'];

                if (!in_array($statusLower, $ignoreStatuses, true)) {
                    $bookings->whereRaw('LOWER(status) = ?', [$statusLower]);
                }
            }

            if (isset($_GET['s'])) {
                $search = trim(sanitize($_GET['s']));

                if ($search === '') {
                    return view('pos.list-bookings', ['bookings' => $bookings->paginate(20)]);
                }

                $bookings->where(function ($query) use ($search) {
                    $like = '%' . $search . '%';

                    $query->where('booking_id', 'like', $like)
                        ->orWhere('customer_name', 'like', $like)
                        ->orWhere('customer_phone', 'like', $like)
                        ->orWhere('tv_brand', 'like', $like)
                        ->orWhere('tv_model', 'like', $like)
                        ->orWhere('issue_type', 'like', $like);
                });
            }

            return view('pos.list-bookings', ['bookings' => $bookings->paginate(20)]);
        }
    }

    public function destroy(Request $request, Booking $booking)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('id'));
            $verify = Booking::where('id', $id);
            if ($verify && $verify->get()->count() > 0) {
                if ($verify->delete()) {
                    return response(json_encode(array("error" => 0, "msg" => "Booking deleted successfully")));
                }
                return response(json_encode(array("error" => 1, "msg" => "Booking not found")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Sorry! something went wrong")));
        }
    }

    public function changeStatus(Request $request)
    {
        if (Auth::check() && isCashier()) {
            $id = sanitize($request->input('id'));
            $verify = Booking::where('id', $id)->first();
            if ($verify) {
                $verify->status = 'converted';
                $verify->save();
                return response(json_encode(array("error" => 0, "msg" => "Booking status changed successfully")));
            }
            return response(json_encode(array("error" => 1, "msg" => "Booking not found")));
        }
    }
}
