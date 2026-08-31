<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

final class CustomerPaths
{
    #[OA\Get(
        path: '/api/customers',
        operationId: 'listCustomers',
        summary: 'List customers',
        description: 'Returns customers ordered from newest to oldest and paginated with 10 items per page. Available to admin and operator users.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number.',
                schema: new OA\Schema(
                    type: 'integer',
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated customer list.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/CustomerCollection'
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
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/customers',
        operationId: 'createCustomer',
        summary: 'Create customer',
        description: 'Creates a customer. Admin role required.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/CreateCustomerRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Customer created successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Customer'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to create customers.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate customer email.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ValidationError'
                )
            ),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/customers/{customer}',
        operationId: 'showCustomer',
        summary: 'Get customer',
        description: 'Returns a customer by ID. Available to admin and operator users.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'customer',
                in: 'path',
                required: true,
                description: 'Customer ID.',
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Customer found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Customer'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Customer not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/customers/{customer}',
        operationId: 'replaceCustomer',
        summary: 'Update customer',
        description: 'Updates supplied customer fields. Admin role required.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'customer',
                in: 'path',
                required: true,
                description: 'Customer ID.',
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UpdateCustomerRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Customer updated successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Customer'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update customers.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Customer not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate customer email.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ValidationError'
                )
            ),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Patch(
        path: '/api/customers/{customer}',
        operationId: 'partiallyUpdateCustomer',
        summary: 'Partially update customer',
        description: 'Updates only supplied customer fields. Admin role required.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'customer',
                in: 'path',
                required: true,
                description: 'Customer ID.',
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/UpdateCustomerRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Customer updated successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Customer'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update customers.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Customer not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate customer email.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ValidationError'
                )
            ),
        ]
    )]
    public function patch(): void
    {
    }

    #[OA\Delete(
        path: '/api/customers/{customer}',
        operationId: 'deleteCustomer',
        summary: 'Delete customer',
        description: 'Deletes a customer only when the customer has no associated orders. Admin role required.',
        tags: ['Customers'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'customer',
                in: 'path',
                required: true,
                description: 'Customer ID.',
                schema: new OA\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1,
                    example: 1
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Customer deleted successfully.'
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthenticatedError'
                )
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to delete customers.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Customer not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Customer cannot be deleted because they have associated orders.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ConflictError'
                )
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}
