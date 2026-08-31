<?php

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

final class CategoryPaths
{
    #[OA\Get(
        path: '/api/categories',
        operationId: 'listCategories',
        summary: 'List categories',
        description: 'Returns categories ordered alphabetically by name and paginated with 10 items per page. Available to admin and operator users.',
        tags: ['Categories'],
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
                description: 'Paginated category list.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/CategoryCollection'
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
        path: '/api/categories',
        operationId: 'createCategory',
        summary: 'Create category',
        description: 'Creates a category. Admin role required.',
        tags: ['Categories'],
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/CreateCategoryRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Category created successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Category'
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
                description: 'The authenticated user is not allowed to create categories.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate category name.',
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
        path: '/api/categories/{category}',
        operationId: 'showCategory',
        summary: 'Get category',
        description: 'Returns a category by ID together with its associated products. Available to admin and operator users.',
        tags: ['Categories'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'Category ID.',
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
                description: 'Category found with associated products.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/CategoryWithProducts'
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
                description: 'Category not found.',
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
        path: '/api/categories/{category}',
        operationId: 'replaceCategory',
        summary: 'Update category',
        description: 'Updates supplied category fields. Admin role required.',
        tags: ['Categories'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'Category ID.',
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
                ref: '#/components/schemas/UpdateCategoryRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category updated successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Category'
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
                description: 'The authenticated user is not allowed to update categories.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate category name.',
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
        path: '/api/categories/{category}',
        operationId: 'partiallyUpdateCategory',
        summary: 'Partially update category',
        description: 'Updates only supplied category fields. Admin role required.',
        tags: ['Categories'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'Category ID.',
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
                ref: '#/components/schemas/UpdateCategoryRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category updated successfully.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Category'
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
                description: 'The authenticated user is not allowed to update categories.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed, including duplicate category name.',
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
        path: '/api/categories/{category}',
        operationId: 'deleteCategory',
        summary: 'Delete category',
        description: 'Deletes a category only when it has no associated products. Admin role required.',
        tags: ['Categories'],
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'path',
                required: true,
                description: 'Category ID.',
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
                description: 'Category deleted successfully.'
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
                description: 'The authenticated user is not allowed to delete categories.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ForbiddenError'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Category not found.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/NotFoundError'
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Category cannot be deleted because it has associated products.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/CategoryDeleteConflictError'
                )
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}
