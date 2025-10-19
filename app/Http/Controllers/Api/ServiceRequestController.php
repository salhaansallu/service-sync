<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);

        $query = ServiceRequest::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        // Calculate stats
        $stats = [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'inProgress' => ServiceRequest::where('status', 'in-progress')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests->items(),
                'stats' => $stats,
                'pagination' => [
                    'total' => $requests->total(),
                    'page' => $requests->currentPage(),
                    'limit' => $requests->perPage(),
                    'totalPages' => $requests->lastPage(),
                ]
            ]
        ], 200);
    }

    public function update(Request $request, $requestId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in-progress,completed',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $serviceRequest = ServiceRequest::where('id', $requestId)
            ->orWhere('request_id', $requestId)
            ->first();

        if (!$serviceRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Service request not found',
                'error' => 'REQUEST_NOT_FOUND'
            ], 404);
        }

        $serviceRequest->status = $request->status;
        if ($request->has('notes')) {
            $serviceRequest->admin_notes = $request->notes;
        }
        $serviceRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Service request updated',
            'data' => [
                'id' => $serviceRequest->request_id,
                'status' => $serviceRequest->status,
                'notes' => $serviceRequest->admin_notes,
                'updatedAt' => $serviceRequest->updated_at->toIso8601String(),
            ]
        ], 200);
    }
}
