<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\orders;
use App\Models\Repairs;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    /**
     * Get all orders for the authenticated user's linked customer
     */
    public function getOrders(Request $request)
    {
        $user = $request->user();

        if (!$user->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'No customer account linked to this user',
                'error' => 'NO_CUSTOMER_LINKED'
            ], 404);
        }

        $customer = $user->customer;
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
                'error' => 'CUSTOMER_NOT_FOUND'
            ], 404);
        }

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $type = $request->query('type'); // 'orders' or 'repairs'

        $response = [
            'success' => true,
            'data' => [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address,
                    'storeCredit' => (float) $customer->store_credit,
                ],
            ]
        ];

        // Get orders if requested or by default
        if (!$type || $type === 'orders') {
            $orders = orders::where('customer', $customer->id)
                ->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $response['data']['orders'] = [
                'items' => $orders->map(function($order) {
                    return $this->formatOrder($order);
                }),
                'pagination' => [
                    'total' => $orders->total(),
                    'page' => $orders->currentPage(),
                    'limit' => $orders->perPage(),
                    'totalPages' => $orders->lastPage(),
                ]
            ];
        }

        // Get repairs if requested or by default
        if (!$type || $type === 'repairs') {
            $repairs = Repairs::where('customer', $customer->id)
                ->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $response['data']['repairs'] = [
                'items' => $repairs->map(function($repair) {
                    return $this->formatRepair($repair);
                }),
                'pagination' => [
                    'total' => $repairs->total(),
                    'page' => $repairs->currentPage(),
                    'limit' => $repairs->perPage(),
                    'totalPages' => $repairs->lastPage(),
                ]
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Get a specific order by ID
     */
    public function getOrder(Request $request, $orderId)
    {
        $user = $request->user();

        if (!$user->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'No customer account linked to this user',
                'error' => 'NO_CUSTOMER_LINKED'
            ], 404);
        }

        $order = orders::where('id', $orderId)
            ->where('customer', $user->customer_id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'error' => 'ORDER_NOT_FOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order, true)
        ], 200);
    }

    /**
     * Get a specific repair by ID
     */
    public function getRepair(Request $request, $repairId)
    {
        $user = $request->user();

        if (!$user->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'No customer account linked to this user',
                'error' => 'NO_CUSTOMER_LINKED'
            ], 404);
        }

        $repair = Repairs::where('id', $repairId)
            ->where('customer', $user->customer_id)
            ->first();

        if (!$repair) {
            return response()->json([
                'success' => false,
                'message' => 'Repair not found',
                'error' => 'REPAIR_NOT_FOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatRepair($repair, true)
        ], 200);
    }

    /**
     * Get customer statistics
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        if (!$user->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'No customer account linked to this user',
                'error' => 'NO_CUSTOMER_LINKED'
            ], 404);
        }

        $customer = $user->customer;

        $stats = [
            'totalOrders' => orders::where('customer', $customer->id)->count(),
            'totalRepairs' => Repairs::where('customer', $customer->id)->count(),
            'pendingRepairs' => Repairs::where('customer', $customer->id)
                ->whereNotIn('status', ['Completed', 'Delivered'])
                ->count(),
            'completedRepairs' => Repairs::where('customer', $customer->id)
                ->whereIn('status', ['Completed', 'Delivered'])
                ->count(),
            'storeCredit' => (float) $customer->store_credit,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }

    /**
     * Format order for API response
     */
    private function formatOrder($order, $detailed = false)
    {
        $data = [
            'id' => $order->id,
            'orderNumber' => $order->order_no ?? null,
            'date' => $order->created_at->toIso8601String(),
            'status' => $order->status ?? 'pending',
            'total' => (float) ($order->total ?? 0),
        ];

        if ($detailed) {
            // Add more detailed information if needed
            $data['items'] = json_decode($order->products ?? '[]');
            $data['paymentMethod'] = $order->payment_method ?? null;
            $data['notes'] = $order->notes ?? null;
        }

        return $data;
    }

    /**
     * Format repair for API response
     */
    private function formatRepair($repair, $detailed = false)
    {
        $data = [
            'id' => $repair->id,
            'billNo' => $repair->bill_no,
            'modelNo' => $repair->model_no,
            'serialNo' => $repair->serial_no,
            'fault' => $repair->fault,
            'status' => $repair->status,
            'total' => (float) $repair->total,
            'advance' => (float) $repair->advance,
            'createdAt' => $repair->created_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['cost'] = (float) $repair->cost;
            $data['delivery'] = (float) $repair->delivery;
            $data['note'] = $repair->note;
            $data['technician'] = $repair->techie;
            $data['warranty'] = $repair->warranty;
            $data['paidDate'] = $repair->paid_date;
            $data['repairedDate'] = $repair->repaired_date;
            $data['hasMultipleFault'] = $repair->has_multiple_fault;
            $data['multipleFault'] = $repair->multiple_fault;
            $data['spares'] = $repair->spares ? json_decode($repair->spares) : null;
            $data['products'] = $repair->products ? json_decode($repair->products) : null;
        }

        return $data;
    }
}
