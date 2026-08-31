<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

final class ProductPaths
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'listProducts',
        summary: 'List products',
        description: 'Returns products ordered alphabetically by name, including their category, and paginated with 10 items per page. Available to admin and operator users.',
        tags: ['Products'],
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
                description: 'Paginated product list.',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductCollection')
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
        path: '/api/products',
        operationId: 'createProduct',
        summary: 'Create product',
        description: 'Creates a product and associates it with an existing category. Admin role required.',
        tags: ['Products'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to create products.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including invalid category, negative price, or negative stock.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/products/{product}',
        operationId: 'showProduct',
        summary: 'Get product',
        description: 'Returns a product by ID together with its category. Available to admin and operator users.',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'Product ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product found.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/products/{product}',
        operationId: 'replaceProduct',
        summary: 'Update product',
        description: 'Updates supplied product fields. Admin role required.',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'Product ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update products.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including invalid category, negative price, or negative stock.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Patch(
        path: '/api/products/{product}',
        operationId: 'partiallyUpdateProduct',
        summary: 'Partially update product',
        description: 'Updates only supplied product fields. Admin role required.',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'Product ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProductRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product')
            ),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to update products.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including invalid category, negative price, or negative stock.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
        ]
    )]
    public function patch(): void
    {
    }

    #[OA\Delete(
        path: '/api/products/{product}',
        operationId: 'deleteProduct',
        summary: 'Delete product',
        description: 'Deletes a product only when it is not associated with any order items. Admin role required.',
        tags: ['Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'product',
                in: 'path',
                required: true,
                description: 'Product ID.',
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Product deleted successfully.'),
            new OA\Response(
                response: 401,
                description: 'Authentication token is missing or invalid.',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthenticatedError')
            ),
            new OA\Response(
                response: 403,
                description: 'The authenticated user is not allowed to delete products.',
                content: new OA\JsonContent(ref: '#/components/schemas/ForbiddenError')
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
                content: new OA\JsonContent(ref: '#/components/schemas/NotFoundError')
            ),
            new OA\Response(
                response: 409,
                description: 'Product cannot be deleted because it is associated with orders.',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductDeleteConflictError')
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}
