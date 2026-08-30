<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->with(['customer', 'items.product'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders)->response();
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $validated = $request->validated();

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
                        'items' => ["Product {$product->name} is inactive."],
                    ]);
                }

                if ($product->stock < $itemData['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for product {$product->name}. Available: {$product->stock}."],
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

            $order->update(['total' => $total]);

            return $order;
        });

        return (new OrderResource($order->load(['customer', 'items.product'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return (new OrderResource($order->load(['customer', 'items.product'])))->response();
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $order->update($request->validated());

        return (new OrderResource($order->fresh()->load(['customer', 'items.product'])))->response();
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

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
