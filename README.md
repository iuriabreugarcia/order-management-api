# Order Management API

[![Tests](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml/badge.svg)](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml)

RESTful API for order management and inventory control, built with Laravel and PHP.
This project demonstrates the implementation of a transactional backend with token-based authentication, role-based authorization, structured validation, standardized API responses, inventory control, order processing, database consistency, OpenAPI documentation, and automated feature tests.

## Features

- Token-based API authentication with Laravel Sanctum
- Role-based authorization with Laravel Policies
- `admin` and `operator` user roles
- Protected business endpoints
- Login and logout with personal access tokens
- Customer management
- Product category management
- Product and inventory management
- Order creation with multiple items
- Automatic order total calculation
- Automatic stock reduction
- Insufficient stock validation
- Inactive product validation
- Database transactions for order processing
- Row locking during inventory updates
- Stock restoration when pending orders are deleted
- Order lifecycle control
- Pagination
- Form Request validation
- API Resources for response transformation
- Eloquent relationships
- OpenAPI 3 documentation
- Interactive Swagger UI
- Bearer/Sanctum authentication documented in Swagger
- OpenAPI contract test coverage
- Demo database seeding
- Automated feature and authorization tests
- Continuous Integration with GitHub Actions

## Tech Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum
- L5-Swagger 11
- swagger-php 6
- Swagger UI 5
- OpenAPI 3.0
- Eloquent ORM
- SQLite
- PHPUnit
- Composer
- GitHub Actions
- REST API

## Domain Model

```text
Customer
   |
   +----< Order
             |
             +----< OrderItem >---- Product
                                      |
                                      v
                                   Category
```

### Main Entities

**Customer**
- name
- email
- phone

**Category**
- name

**Product**
- category
- name
- description
- price
- stock
- active status

**Order**
- customer
- status
- total

**OrderItem**
- order
- product
- quantity
- unit price
- subtotal

## Business Rules

### Transactional Order Processing

Order creation is executed inside a database transaction.

When an order is created, the API:

1. Validates the customer and requested items.
2. Locks each product row for update.
3. Checks whether the product is active.
4. Checks available inventory.
5. Preserves the product price in the order item.
6. Calculates each item subtotal.
7. Reduces product inventory.
8. Calculates the final order total.
9. Commits the operation only when the entire order is valid.

If any item cannot be processed, the transaction is rolled back.

### Inventory Protection

An order cannot be created when the requested quantity exceeds available stock.

Example:

```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 100
    }
  ]
}
```

The API returns a validation error and inventory remains unchanged.

Inactive products also cannot be included in new orders.

### Price History

The current product price is copied to `order_items.unit_price` when an order is created.

This preserves the original transaction value even if the product price changes later.

### Order Deletion

Only orders with `pending` status can be deleted.

When a pending order is deleted, its quantities are returned to inventory inside a database transaction.

Orders that have already moved to another lifecycle status cannot be deleted through this operation.

## Authentication

The API uses Laravel Sanctum for token-based authentication.

Business endpoints for customers, categories, products, and orders require an authenticated user.

### Login

```http
POST /api/login
```

Example request:

```json
{
  "email": "user@example.com",
  "password": "your-password"
}
```

> The credentials above are illustrative. Create a local user before testing the login endpoint.

A successful login returns the authenticated user and a personal access token:

```json
{
  "user": {
    "id": 1,
    "name": "Example User",
    "email": "user@example.com",
    "role": "operator"
  },
  "token": "your-personal-access-token"
}
```

### Authenticated Requests

Send the returned token using the `Authorization` header:

```http
Authorization: Bearer your-personal-access-token
Accept: application/json
```

Example:

```bash
curl \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  http://127.0.0.1:8000/api/orders
```

Requests to protected endpoints without valid authentication return an unauthorized response.

### Current User

```http
GET /api/user
```

Returns the currently authenticated user.

### Logout

```http
POST /api/logout
```

The current personal access token is revoked during logout.

## Authorization and Roles

Authorization is implemented with Laravel Policies. The application currently supports two roles:

- `admin` — full access to master data and order operations.
- `operator` — read access to business resources and operational access to create and update orders.

### Permission Matrix

| Resource / Action | Admin | Operator |
|---|:---:|:---:|
| List and view customers | Yes | Yes |
| Create, update, or delete customers | Yes | No |
| List and view categories | Yes | Yes |
| Create, update, or delete categories | Yes | No |
| List and view products | Yes | Yes |
| Create, update, or delete products | Yes | No |
| List and view orders | Yes | Yes |
| Create orders | Yes | Yes |
| Update order status | Yes | Yes |
| Delete pending orders | Yes | No |

Unauthorized operations return an HTTP `403 Forbidden` response.

## OpenAPI / Swagger Documentation

The API is documented with OpenAPI 3 using L5-Swagger and swagger-php PHP attributes.

The specification covers:

- Sanctum Bearer authentication
- authentication endpoints
- customers
- categories
- products
- orders
- request schemas
- response schemas
- pagination
- role-sensitive operations
- HTTP `401`, `403`, `404`, `409`, and `422` responses
- example request and response payloads
- order and inventory business rules

### Swagger UI

Start the application:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000/api/documentation
```

Swagger UI provides an interactive view of the API contract and an **Authorize** action for Sanctum Bearer tokens.

Authenticate through:

```http
POST /api/login
```

Copy the returned personal access token and use it as the Bearer token in Swagger UI.

### Generate the OpenAPI Specification

The OpenAPI JSON is generated from PHP attributes:

```bash
php artisan l5-swagger:generate
```

The generated specification is written to:

```text
storage/api-docs/api-docs.json
```

This file is intentionally excluded from version control because it is a generated artifact. The PHP OpenAPI definitions under `app/OpenApi` are the source of truth.

The automated test suite also regenerates and validates the specification to detect missing paths, schemas, authentication configuration, and critical response codes.

## API Endpoints

Except for `/api/login`, the endpoints below require Sanctum authentication. Individual operations are also subject to the role-based authorization rules above.

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/login` | Authenticate and create an API token |
| GET | `/api/user` | Return the authenticated user |
| POST | `/api/logout` | Revoke the current API token |

### Customers

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/customers` | List customers |
| POST | `/api/customers` | Create customer |
| GET | `/api/customers/{id}` | Show customer |
| PUT/PATCH | `/api/customers/{id}` | Update customer |
| DELETE | `/api/customers/{id}` | Delete customer |

### Categories

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | List categories |
| POST | `/api/categories` | Create category |
| GET | `/api/categories/{id}` | Show category |
| PUT/PATCH | `/api/categories/{id}` | Update category |
| DELETE | `/api/categories/{id}` | Delete category |

### Products

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | List products |
| POST | `/api/products` | Create product |
| GET | `/api/products/{id}` | Show product |
| PUT/PATCH | `/api/products/{id}` | Update product |
| DELETE | `/api/products/{id}` | Delete product |

### Orders

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/orders` | List orders |
| POST | `/api/orders` | Create order |
| GET | `/api/orders/{id}` | Show order |
| PUT/PATCH | `/api/orders/{id}` | Update order status |
| DELETE | `/api/orders/{id}` | Delete pending order |

## Creating an Order

Authentication is required. Both `admin` and `operator` users can create orders.

Example request:

```json
{
  "customer_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 3
    }
  ]
}
```

Example response:

```json
{
  "id": 1,
  "customer_id": 1,
  "status": "pending",
  "total": "25.50",
  "customer": {
    "id": 1,
    "name": "Example Customer",
    "email": "customer@example.com",
    "phone": null
  },
  "items": [
    {
      "id": 1,
      "product_id": 1,
      "quantity": 3,
      "unit_price": "8.50",
      "subtotal": "25.50",
      "product": {
        "id": 1,
        "name": "Orange Juice",
        "price": "8.50",
        "stock": 17,
        "active": true
      }
    }
  ]
}
```

## Validation and API Resources

Request validation is organized through dedicated Laravel Form Request classes.

The API uses resources to provide consistent response transformation for:

- customers
- categories
- products
- orders
- order items

This keeps validation and presentation concerns outside the controllers and makes the HTTP layer easier to maintain and test.

## Installation

Clone the repository:

```bash
git clone https://github.com/iuriabreugarcia/order-management-api.git
cd order-management-api
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Create and populate the database:

```bash
php artisan migrate:fresh --seed
```

Generate the OpenAPI specification:

```bash
php artisan l5-swagger:generate
```

Start the development server:

```bash
php artisan serve
```

The API will be available by default at:

```text
http://127.0.0.1:8000/api
```

Swagger UI will be available at:

```text
http://127.0.0.1:8000/api/documentation
```

## Demo Data

The project includes factories and a demo database seeder.

Running:

```bash
php artisan migrate:fresh --seed
```

creates:

- 10 demo customers
- 3 categories
- 5 products

This provides sample business data for local development and API exploration.

The demo data seeder does not currently create an authentication user.

## Automated Tests

Run:

```bash
php artisan test
```

The automated test suite covers authentication, authorization, CRUD operations, validation, API response structures, inventory behavior, transactional order processing, and the generated OpenAPI contract.

Coverage includes:

- login with valid credentials
- rejection of invalid credentials
- protection of authenticated endpoints
- authenticated access using Sanctum
- token revocation during logout
- role-based access control for `admin` and `operator`
- operator restrictions on master-data management and order deletion
- administrator access to protected management operations
- customer creation, listing, retrieval, update, and deletion
- customer email uniqueness validation
- category creation, listing, retrieval, update, and deletion
- prevention of category deletion when products exist
- category name uniqueness validation
- product creation, listing, retrieval, update, and deletion
- product category validation
- product price and stock validation
- successful order creation
- order listing and retrieval
- order status updates
- API Resource response structures
- automatic inventory reduction
- inactive product rejection
- insufficient stock rejection
- transaction rollback behavior
- stock restoration when a pending order is deleted
- protection against deleting non-pending orders
- OpenAPI specification generation
- required OpenAPI paths and operations
- Sanctum Bearer security scheme
- required OpenAPI schemas
- critical `422` and `409` response documentation
- Swagger UI route availability

Current test suite:

```text
41 tests
351 assertions
```

## Continuous Integration

The project uses GitHub Actions to automatically run the test suite on pushes and pull requests to the `main` branch.

The CI workflow:

1. Checks out the repository.
2. Configures PHP 8.4.
3. Installs Composer dependencies.
4. Creates the application environment.
5. Generates the Laravel application key.
6. Runs the complete automated test suite.

Because the OpenAPI contract test runs as part of the PHPUnit suite, CI also verifies that the Swagger/OpenAPI specification can still be generated successfully.

The status badge at the top of this README reflects the current CI result.

## Project Structure

```text
app/
|-- Http/
|   |-- Controllers/
|   |   `-- Api/
|   |       |-- AuthController.php
|   |       |-- CategoryController.php
|   |       |-- CustomerController.php
|   |       |-- OrderController.php
|   |       `-- ProductController.php
|   |-- Requests/
|   `-- Resources/
|-- Models/
|-- OpenApi/
|   |-- OpenApiSpec.php
|   |-- Paths/
|   |   |-- AuthPaths.php
|   |   |-- CategoryPaths.php
|   |   |-- CustomerPaths.php
|   |   |-- OrderPaths.php
|   |   `-- ProductPaths.php
|   `-- Schemas/
|       |-- AuthSchemas.php
|       |-- CategorySchemas.php
|       |-- CommonSchemas.php
|       |-- CustomerSchemas.php
|       |-- OrderSchemas.php
|       `-- ProductSchemas.php
|-- Policies/
|   |-- CategoryPolicy.php
|   |-- CustomerPolicy.php
|   |-- OrderPolicy.php
|   `-- ProductPolicy.php
config/
`-- l5-swagger.php
database/
|-- factories/
|-- migrations/
`-- seeders/
routes/
`-- api.php
tests/
|-- Feature/
|   |-- AuthApiTest.php
|   |-- AuthorizationApiTest.php
|   |-- CategoryApiTest.php
|   |-- CustomerApiTest.php
|   |-- OpenApiDocumentationTest.php
|   |-- OrderApiTest.php
|   `-- ProductApiTest.php
`-- Unit/
.github/
`-- workflows/
    `-- tests.yml
```

## Engineering Decisions

This project intentionally goes beyond a basic CRUD implementation.

Some of the technical decisions include:

- Laravel Sanctum personal access tokens for API authentication
- middleware protection for business endpoints
- token revocation during logout
- role-based access control for `admin` and `operator`
- operator restrictions on master-data management and order deletion
- administrator access to protected management operations
- dedicated Form Request classes for validation
- API Resources for response transformation
- OpenAPI definitions isolated from application controllers
- PHP attributes as the source of truth for API documentation
- generated OpenAPI JSON excluded from version control
- automated OpenAPI contract validation
- database transactions to preserve consistency
- `lockForUpdate()` during inventory processing
- historical price storage in order items
- foreign-key constraints
- model relationships through Eloquent
- controlled order lifecycle
- inventory restoration rules
- automated feature tests for CRUD operations and business rules
- response structure validation
- continuous integration with GitHub Actions

These decisions are intended to demonstrate backend development focused not only on endpoints, but also on authentication, authorization, API contract documentation, separation of concerns, data integrity, concurrency, business rules, and automated quality assurance.

## Roadmap

- [x] Token-based API authentication
- [x] Form Request validation
- [x] API Resources
- [x] CRUD feature test coverage
- [x] Order and inventory business-rule tests
- [x] Continuous Integration with GitHub Actions
- [x] Authorization policies and roles
- [x] OpenAPI / Swagger documentation
- [ ] Docker environment
- [ ] Additional edge-case and authorization tests

## Author

**Iuri Abreu e Garcia**

Backend Development | Systems Analysis | Data Analysis
