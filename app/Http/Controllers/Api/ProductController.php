<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $search = $request->query('search');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $inStock = $request->query('inStock');

        $query = Products::query();

        if ($category) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pro_name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($inStock !== null) {
            if ($inStock === 'true' || $inStock === '1') {
                $query->where('qty', '>', 0);
            }
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $formattedProducts = $products->map(function($product) {
            return $this->formatProductResponse($product);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $formattedProducts,
                'pagination' => [
                    'total' => $products->total(),
                    'page' => $products->currentPage(),
                    'limit' => $products->perPage(),
                    'totalPages' => $products->lastPage(),
                ]
            ]
        ], 200);
    }

    public function show($productId)
    {
        $product = Products::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'PRODUCT_NOT_FOUND'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProductResponse($product, true)
        ], 200);
    }

    private function formatProductResponse($product, $detailed = false)
    {
        $data = [
            'id' => 'product_' . $product->id,
            'name' => $product->pro_name,
            'modelNumber' => $product->sku,
            'price' => (float) $product->price,
            'stock' => (int) $product->qty,
            'images' => [$product->pro_image],
            'isActive' => true,
            'createdAt' => $product->created_at->toIso8601String(),
            'updatedAt' => $product->updated_at->toIso8601String(),
        ];

        if ($detailed) {
            $data['description'] = $product->pro_name; // You can add a description field to products table
            $data['category'] = 'TV Parts'; // Default category, can be enhanced
        }

        return $data;
    }
}
