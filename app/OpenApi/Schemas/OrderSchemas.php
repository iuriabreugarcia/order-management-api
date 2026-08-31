<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderItemProduct',
    type: 'object',
    required: [
        'id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'active',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Orange Juice'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: '500ml'),
        new OA\Property(
            property: 'price',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            description: 'Current product price serialized with two fractional digits.',
            example: '8.50'
        ),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 17),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'OrderItem',
    type: 'object',
    required: [
        'id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'product',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 3),
        new OA\Property(
            property: 'unit_price',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            description: 'Price snapshot captured when the order was created.',
            example: '8.50'
        ),
        new OA\Property(
            property: 'subtotal',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            description: 'unit_price multiplied by quantity.',
            example: '25.50'
        ),
        new OA\Property(property: 'product', ref: '#/components/schemas/OrderItemProduct'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'Order',
    type: 'object',
    required: [
        'id',
        'customer_id',
        'status',
        'total',
        'customer',
        'items',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'customer_id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'processing', 'completed', 'cancelled'],
            example: 'pending'
        ),
        new OA\Property(
            property: 'total',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            description: 'Order total calculated from item subtotals.',
            example: '25.50'
        ),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItem')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'CreateOrderItemRequest',
    type: 'object',
    required: ['product_id', 'quantity'],
    properties: [
        new OA\Property(
            property: 'product_id',
            type: 'integer',
            format: 'int64',
            minimum: 1,
            description: 'ID of an existing product.',
            example: 1
        ),
        new OA\Property(
            property: 'quantity',
            type: 'integer',
            minimum: 1,
            example: 3
        ),
    ]
)]
#[OA\Schema(
    schema: 'CreateOrderRequest',
    type: 'object',
    required: ['customer_id', 'items'],
    properties: [
        new OA\Property(
            property: 'customer_id',
            type: 'integer',
            format: 'int64',
            minimum: 1,
            description: 'ID of an existing customer.',
            example: 1
        ),
        new OA\Property(
            property: 'items',
            type: 'array',
            minItems: 1,
            items: new OA\Items(ref: '#/components/schemas/CreateOrderItemRequest')
        ),
    ]
)]
#[OA\Schema(
    schema: 'UpdateOrderRequest',
    type: 'object',
    required: ['status'],
    properties: [
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'processing', 'completed', 'cancelled'],
            example: 'processing'
        ),
    ]
)]
#[OA\Schema(
    schema: 'OrderCollection',
    type: 'object',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Order')
        ),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ]
)]
#[OA\Schema(
    schema: 'OrderDeleteConflictError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Only pending orders can be deleted.'
        ),
    ]
)]
final class OrderSchemas
{
}
