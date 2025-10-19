<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        $items = $cart->items->map(function($item) {
            return [
                'id' => 'cart_item_' . $item->id,
                'productId' => 'product_' . $item->product_id,
                'product' => [
                    'id' => 'product_' . $item->product->id,
                    'name' => $item->product->pro_name,
                    'price' => (float) $item->product->price,
                    'images' => [$item->product->pro_image],
                    'stock' => (int) $item->product->qty,
                ],
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'summary' => $cart->calculateSummary(),
            ]
        ], 200);
    }

    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
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
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

        $product = Products::find($request->productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'PRODUCT_NOT_FOUND'
            ], 404);
        }

        // Check if product has enough stock
        if ($product->qty < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock',
                'error' => 'INSUFFICIENT_STOCK'
            ], 400);
        }

        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => [
                'id' => 'cart_item_' . $cartItem->id,
                'productId' => 'product_' . $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'subtotal' => (float) $cartItem->subtotal,
            ]
        ], 201);
    }

    public function updateItem(Request $request, $itemId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
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
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
                'error' => 'CART_NOT_FOUND'
            ], 404);
        }

        $cartItem = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
                'error' => 'ITEM_NOT_FOUND'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated',
            'data' => [
                'id' => 'cart_item_' . $cartItem->id,
                'quantity' => $cartItem->quantity,
                'subtotal' => (float) $cartItem->subtotal,
            ]
        ], 200);
    }

    public function removeItem(Request $request, $itemId)
    {
        $user = $request->user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
                'error' => 'CART_NOT_FOUND'
            ], 404);
        }

        $cartItem = CartItem::where('id', $itemId)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
                'error' => 'ITEM_NOT_FOUND'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ], 200);
    }

    public function clearCart(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart;

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ], 200);
    }
}
