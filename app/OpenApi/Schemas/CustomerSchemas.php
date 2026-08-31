<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Customer',
    type: 'object',
    required: [
        'id',
        'name',
        'email',
        'phone',
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
            example: 'John Customer'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            nullable: true,
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            maxLength: 30,
            nullable: true,
            example: '71999999999'
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
    schema: 'CreateCustomerRequest',
    type: 'object',
    required: ['name'],
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'John Customer'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            nullable: true,
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            maxLength: 30,
            nullable: true,
            example: '71999999999'
        ),
    ]
)]
#[OA\Schema(
    schema: 'UpdateCustomerRequest',
    type: 'object',
    description: 'Only supplied fields are updated. Name cannot be null when supplied.',
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Updated Customer'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            nullable: true,
            example: 'updated@example.com'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            maxLength: 30,
            nullable: true,
            example: '71888888888'
        ),
    ]
)]
#[OA\Schema(
    schema: 'CustomerCollection',
    type: 'object',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/Customer'
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
final class CustomerSchemas
{
}
