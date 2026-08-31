<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

final class OrderPaths
{
    #[OA\Get(
        path: '/api/orders',
        operationId: 'listOrders',
        summary: 'List orders',
        description: 'Returns orders from newest to oldest, including customer and order-item product data, paginated with 10 items per page. Available to admin and operator users.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number.',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated order list.',
                content: new OA\JsonContent(ref: '#/components/schemas/OrderCollection')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/orders',
        operationId: 'createOrder',
        summary: 'Create order',
        description: 'Creates a pending order with one or more items. Product prices are captured as unit-price snapshots, item subtotals and the order total are calculated automatically, and stock is reduced transactionally. Inactive products and insufficient stock are rejected and the transaction is rolled back. Available to admin and operator users.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateOrderRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created successfully. Initial status is pending and stock has been reduced.',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to create orders.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed. Possible causes include invalid customer/product IDs, empty items, quantity below 1, inactive product, or insufficient stock.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/orders/{order}',
        operationId: 'showOrder',
        summary: 'Get order',
        description: 'Returns an order by ID including its customer, items, and each item product. Available to admin and operator users.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order found.',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/orders/{order}',
        operationId: 'replaceOrderStatus',
        summary: 'Update order status',
        description: 'Updates only the order status. Valid values are pending, processing, completed, and cancelled. Available to admin and operator users.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order status updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update orders.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed because status is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Patch(
        path: '/api/orders/{order}',
        operationId: 'partiallyUpdateOrderStatus',
        summary: 'Update order status',
        description: 'Updates only the order status. The status field is required and must be pending, processing, completed, or cancelled. Available to admin and operator users.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateOrderRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order status updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update orders.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed because status is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function patch(): void
    {
    }

    #[OA\Delete(
        path: '/api/orders/{order}',
        operationId: 'deleteOrder',
        summary: 'Delete pending order',
        description: 'Deletes a pending order and restores each ordered quantity to product stock within a database transaction. Only admin users may delete orders. Orders in processing, completed, or cancelled status cannot be deleted.',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order',
                in: 'path',
                required: true,
                description: 'Order ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Pending order deleted successfully and stock restored.'
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'Only admin users may delete orders.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 409,
                description: 'The order cannot be deleted because its status is not pending.',
                content: new OA\JsonContent(ref: '#/components/schemas/OrderDeleteConflictError')
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}
