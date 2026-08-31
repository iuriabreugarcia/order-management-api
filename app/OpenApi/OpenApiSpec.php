<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Order Management API',
    description: 'REST API for managing customers, categories, products, orders, inventory, authentication, and role-based authorization.'
)]
#[OA\Server(
    url: '/',
    description: 'Current application server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Laravel Sanctum personal access token. Authenticate with POST /api/login and use the returned token as a Bearer token.'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'Authentication and authenticated-user operations.'
)]
#[OA\Tag(
    name: 'Customers',
    description: 'Customer management operations.'
)]
#[OA\Tag(
    name: 'Categories',
    description: 'Product category management operations.'
)]
#[OA\Tag(
    name: 'Products',
    description: 'Product and inventory management operations.'
)]
#[OA\Tag(
    name: 'Orders',
    description: 'Order processing and order lifecycle operations.'
)]
final class OpenApiSpec
{
}