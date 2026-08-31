<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UnauthenticatedError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Unauthenticated.'
        ),
    ]
)]
#[OA\Schema(
    schema: 'ForbiddenError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'This action is unauthorized.'
        ),
    ]
)]
#[OA\Schema(
    schema: 'NotFoundError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Resource not found.'
        ),
    ]
)]
#[OA\Schema(
    schema: 'ConflictError',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'The requested operation conflicts with the current resource state.'
        ),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'The given data was invalid.'
        ),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(
                    type: 'string'
                )
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'PaginationLink',
    type: 'object',
    required: ['url', 'label', 'active'],
    properties: [
        new OA\Property(
            property: 'url',
            type: 'string',
            nullable: true,
            example: 'http://127.0.0.1:8000/api/resources?page=1'
        ),
        new OA\Property(
            property: 'label',
            type: 'string',
            example: '1'
        ),
        new OA\Property(
            property: 'active',
            type: 'boolean',
            example: true
        ),
    ]
)]
#[OA\Schema(
    schema: 'PaginationLinks',
    type: 'object',
    required: ['first', 'last', 'prev', 'next'],
    properties: [
        new OA\Property(
            property: 'first',
            type: 'string',
            example: 'http://127.0.0.1:8000/api/resources?page=1'
        ),
        new OA\Property(
            property: 'last',
            type: 'string',
            example: 'http://127.0.0.1:8000/api/resources?page=3'
        ),
        new OA\Property(
            property: 'prev',
            type: 'string',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'next',
            type: 'string',
            nullable: true,
            example: 'http://127.0.0.1:8000/api/resources?page=2'
        ),
    ]
)]
#[OA\Schema(
    schema: 'PaginationMeta',
    type: 'object',
    required: [
        'current_page',
        'from',
        'last_page',
        'links',
        'path',
        'per_page',
        'to',
        'total',
    ],
    properties: [
        new OA\Property(
            property: 'current_page',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'from',
            type: 'integer',
            nullable: true,
            example: 1
        ),
        new OA\Property(
            property: 'last_page',
            type: 'integer',
            example: 3
        ),
        new OA\Property(
            property: 'links',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/PaginationLink'
            )
        ),
        new OA\Property(
            property: 'path',
            type: 'string',
            example: 'http://127.0.0.1:8000/api/resources'
        ),
        new OA\Property(
            property: 'per_page',
            type: 'integer',
            example: 10
        ),
        new OA\Property(
            property: 'to',
            type: 'integer',
            nullable: true,
            example: 10
        ),
        new OA\Property(
            property: 'total',
            type: 'integer',
            example: 25
        ),
    ]
)]
final class CommonSchemas
{
}
