<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->paginate(10);

        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::create($validated);

        return response()->json(
            $product->load('category'),
            201
        );
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(
            $product->load('category')
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        $product->update($validated);

        return response()->json(
            $product->fresh()->load('category')
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => 'Product cannot be deleted because it is associated with orders.',
            ], 409);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}