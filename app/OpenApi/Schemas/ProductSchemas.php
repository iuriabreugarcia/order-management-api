<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    type: 'object',
    required: [
        'id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'active',
        'category',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', format: 'int64', example: 1),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Wireless Keyboard'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Compact wireless keyboard.'),
        new OA\Property(
            property: 'price',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            description: 'Decimal price serialized with two fractional digits.',
            example: '149.90'
        ),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 25),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-30T15:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'CreateProductRequest',
    type: 'object',
    required: ['category_id', 'name', 'price', 'stock'],
    properties: [
        new OA\Property(
            property: 'category_id',
            type: 'integer',
            format: 'int64',
            minimum: 1,
            description: 'ID of an existing category.',
            example: 1
        ),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Wireless Keyboard'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Compact wireless keyboard.'),
        new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0, example: 149.90),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 25),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ]
)]
#[OA\Schema(
    schema: 'UpdateProductRequest',
    type: 'object',
    description: 'Only supplied fields are updated. category_id, name, price and stock cannot be null when supplied.',
    properties: [
        new OA\Property(
            property: 'category_id',
            type: 'integer',
            format: 'int64',
            minimum: 1,
            description: 'ID of an existing category.',
            example: 2
        ),
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Mechanical Keyboard'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Updated product description.'),
        new OA\Property(property: 'price', type: 'number', format: 'float', minimum: 0, example: 199.90),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 40),
        new OA\Property(property: 'active', type: 'boolean', example: false),
    ]
)]
#[OA\Schema(
    schema: 'ProductCollection',
    type: 'object',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Product')
        ),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ]
)]
#[OA\Schema(
    schema: 'ProductDeleteConflictError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Product cannot be deleted because it is associated with orders.'
        ),
    ]
)]
final class ProductSchemas
{
}
