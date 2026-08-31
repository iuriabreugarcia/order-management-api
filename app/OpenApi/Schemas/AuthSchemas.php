<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    type: 'object',
    required: [
        'id',
        'name',
        'email',
        'role',
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
            example: 'Demo Admin'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            example: 'admin@example.com'
        ),
        new OA\Property(
            property: 'email_verified_at',
            type: 'string',
            format: 'date-time',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'role',
            type: 'string',
            enum: ['admin', 'operator'],
            example: 'admin'
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
    schema: 'LoginRequest',
    type: 'object',
    required: ['email', 'password'],
    properties: [
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            example: 'admin@example.com'
        ),
        new OA\Property(
            property: 'password',
            type: 'string',
            format: 'password',
            example: 'password'
        ),
    ]
)]
#[OA\Schema(
    schema: 'LoginResponse',
    type: 'object',
    required: ['user', 'token'],
    properties: [
        new OA\Property(
            property: 'user',
            ref: '#/components/schemas/User'
        ),
        new OA\Property(
            property: 'token',
            type: 'string',
            description: 'Laravel Sanctum personal access token.',
            example: '1|exampleSanctumPersonalAccessToken'
        ),
    ]
)]
#[OA\Schema(
    schema: 'LogoutResponse',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(
            property: 'message',
            type: 'string',
            example: 'Logged out successfully.'
        ),
    ]
)]
final class AuthSchemas
{
}
