<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OpenApiDocumentationTest extends TestCase
{
    public function test_openapi_documentation_can_be_generated_and_contains_core_api_contract(): void
    {
        $exitCode = Artisan::call('l5-swagger:generate');

        $this->assertSame(0, $exitCode);

        $specPath = storage_path('api-docs/api-docs.json');

        $this->assertFileExists($specPath);

        $spec = json_decode(
            (string) file_get_contents($specPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame('3.0.0', $spec['openapi']);
        $this->assertSame('Order Management API', $spec['info']['title']);

        $securityScheme = $spec['components']['securitySchemes']['sanctum'];

        $this->assertSame('http', $securityScheme['type']);
        $this->assertSame('bearer', $securityScheme['scheme']);
        $this->assertSame('Sanctum', $securityScheme['bearerFormat']);

        $expectedOperations = [
            '/api/login' => ['post'],
            '/api/user' => ['get'],
            '/api/logout' => ['post'],

            '/api/customers' => ['get', 'post'],
            '/api/customers/{customer}' => [
                'get',
                'put',
                'patch',
                'delete',
            ],

            '/api/categories' => ['get', 'post'],
            '/api/categories/{category}' => [
                'get',
                'put',
                'patch',
                'delete',
            ],

            '/api/products' => ['get', 'post'],
            '/api/products/{product}' => [
                'get',
                'put',
                'patch',
                'delete',
            ],

            '/api/orders' => ['get', 'post'],
            '/api/orders/{order}' => [
                'get',
                'put',
                'patch',
                'delete',
            ],
        ];

        foreach ($expectedOperations as $path => $methods) {
            $this->assertArrayHasKey($path, $spec['paths']);

            foreach ($methods as $method) {
                $this->assertArrayHasKey(
                    $method,
                    $spec['paths'][$path],
                    "{$method} {$path} is missing from the OpenAPI specification."
                );
            }
        }

        $this->assertArrayNotHasKey(
            'security',
            $spec['paths']['/api/login']['post']
        );

        $this->assertSame(
            [['sanctum' => []]],
            $spec['paths']['/api/customers']['get']['security']
        );

        $this->assertSame(
            [['sanctum' => []]],
            $spec['paths']['/api/orders']['post']['security']
        );

        $this->assertArrayHasKey(
            422,
            $spec['paths']['/api/orders']['post']['responses']
        );

        $this->assertArrayHasKey(
            409,
            $spec['paths']['/api/orders/{order}']['delete']['responses']
        );

        $expectedSchemas = [
            'User',
            'Customer',
            'Category',
            'Product',
            'Order',
            'OrderItem',
            'LoginRequest',
            'LoginResponse',
            'CreateCustomerRequest',
            'CreateCategoryRequest',
            'CreateProductRequest',
            'CreateOrderRequest',
            'UpdateOrderRequest',
            'UnauthenticatedError',
            'ForbiddenError',
            'NotFoundError',
            'ConflictError',
            'ValidationError',
        ];

        foreach ($expectedSchemas as $schema) {
            $this->assertArrayHasKey(
                $schema,
                $spec['components']['schemas'],
                "Schema {$schema} is missing from the OpenAPI specification."
            );
        }

        $this->get('/api/documentation')
            ->assertOk();
    }
}