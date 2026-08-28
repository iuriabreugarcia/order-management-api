<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::query()
            ->latest()
            ->paginate(10);

        return CustomerResource::collection($customers)->response();
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customer = Customer::create($validated);

        return (new CustomerResource($customer))
             ->response()
             ->setStatusCode(201);
        }

    public function show(Customer $customer): JsonResponse
    {
        return (new CustomerResource($customer))->response();
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $validated = $request->validated();

        $customer->update($validated);

        return (new CustomerResource($customer))->response();
    }

    public function destroy(Customer $customer): JsonResponse
    {
        if ($customer->orders()->exists()) {
            return response()->json([
                'message' => 'Customer cannot be deleted because they have associated orders.',
            ], 409);
        }

        $customer->delete();

        return response()->json(null, 204);
    }
}