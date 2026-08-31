<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    type: 'object',
    required: [
        'id',
        'name',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            format: 'int64',
            example: 1
        ),
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Electronics'
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            example: '2026-08-30T15:00:00.000000Z'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            example: '2026-08-30T15:00:00.000000Z'
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategoryProduct',
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
        new OA\Property(
            property: 'id',
            type: 'integer',
            format: 'int64',
            example: 10
        ),
        new OA\Property(
            property: 'category_id',
            type: 'integer',
            format: 'int64',
            example: 1
        ),
        new OA\Property(
            property: 'name',
            type: 'string',
            example: 'Wireless Keyboard'
        ),
        new OA\Property(
            property: 'description',
            type: 'string',
            nullable: true,
            example: 'Compact wireless keyboard.'
        ),
        new OA\Property(
            property: 'price',
            type: 'string',
            pattern: '^\d+\.\d{2}$',
            example: '149.90'
        ),
        new OA\Property(
            property: 'stock',
            type: 'integer',
            minimum: 0,
            example: 25
        ),
        new OA\Property(
            property: 'active',
            type: 'boolean',
            example: true
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            example: '2026-08-30T15:00:00.000000Z'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            example: '2026-08-30T15:00:00.000000Z'
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategoryWithProducts',
    allOf: [
        new OA\Schema(
            ref: '#/components/schemas/Category'
        ),
        new OA\Schema(
            type: 'object',
            required: ['products'],
            properties: [
                new OA\Property(
                    property: 'products',
                    type: 'array',
                    items: new OA\Items(
                        ref: '#/components/schemas/CategoryProduct'
                    )
                ),
            ]
        ),
    ]
)]
#[OA\Schema(
    schema: 'CreateCategoryRequest',
    type: 'object',
    required: ['name'],
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Electronics'
        ),
    ]
)]
#[OA\Schema(
    schema: 'UpdateCategoryRequest',
    type: 'object',
    description: 'Only supplied fields are updated. Name cannot be null or empty when supplied.',
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Consumer Electronics'
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategoryCollection',
    type: 'object',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Category'
            )
        ),
        new OA\Property(
            property: 'links',
            ref: '#/components/schemas/PaginationLinks'
        ),
        new OA\Property(
            property: 'meta',
            ref: '#/components/schemas/PaginationMeta'
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategoryDeleteConflictError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Category cannot be deleted because it has associated products.'
        ),
    ]
)]
final class CategorySchemas
{
}
