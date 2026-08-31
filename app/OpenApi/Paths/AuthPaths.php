<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

final class AuthPaths
{
    #[OA\Post(
        path: '/api/login',
        operationId: 'login',
        summary: 'Authenticate user',
        description: 'Validates the supplied credentials and issues a Laravel Sanctum personal access token.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LoginRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authentication successful.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LoginResponse'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed or the supplied credentials are incorrect.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ValidationError'
                )
            ),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Get(
        path: '/api/user',
        operationId: 'getAuthenticatedUser',
        summary: 'Get authenticated user',
        description: 'Returns the user associated with the supplied Sanctum Bearer token.',
        tags: ['Authentication'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/User'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
        ]
    )]
    public function user(): void
    {
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'logout',
        summary: 'Logout authenticated user',
        description: 'Revokes the Sanctum personal access token used for the current request.',
        tags: ['Authentication'],
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/LogoutResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
        ]
    )]
    public function logout(): void
    {
    }
}