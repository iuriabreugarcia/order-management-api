<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with(['customer', 'items.product'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($validated) {

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'status' => 'pending',
                'total' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $itemData) {

                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($itemData['product_id']);

                if (!$product->active) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Product {$product->name} is inactive.",
                        ],
                    ]);
                }

                if ($product->stock < $itemData['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for product {$product->name}. Available: {$product->stock}.",
                        ],
                    ]);
                }

                $quantity = $itemData['quantity'];
                $unitPrice = $product->price;
                $subtotal = $unitPrice * $quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $quantity);

                $total += $subtotal;
            }

            $order->update([
                'total' => $total,
            ]);

            return $order;
        });

        return response()->json(
            $order->load(['customer', 'items.product']),
            201
        );
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(
            $order->load(['customer', 'items.product'])
        );
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:pending,processing,completed,cancelled',
            ],
        ]);

        $order->update($validated);

        return response()->json(
            $order->fresh()->load(['customer', 'items.product'])
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be deleted.',
            ], 409);
        }

        DB::transaction(function () use ($order) {

            $order->load('items');

            foreach ($order->items as $item) {
                Product::query()
                    ->whereKey($item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $order->delete();
        });

        return response()->json(null, 204);
    }
}