<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->paginate(10);

        return ProductResource::collection($products)->response();
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::create($validated);

	return (new ProductResource(
    	    $product->load('category')
	))

    	    ->response()
    	    ->setStatusCode(201);
        
    }

    public function show(Product $product): JsonResponse
    {
        return (new ProductResource(
    	    $product->load('category')
	))->response();
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        $product->update($validated);

        return (new ProductResource(
    	    $product->fresh()->load('category')
	))->response();
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