# Order Management API

[![Tests](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml/badge.svg)](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml)

A portfolio-grade REST API for **order management and inventory control**, built with Laravel and PHP.

The project goes beyond basic CRUD by implementing transactional order processing, inventory protection, role-based authorization, OpenAPI documentation, automated testing, continuous integration, and a reproducible Docker development environment.

## Portfolio Highlights

- **Transactional order processing** with database rollback on failure
- **Inventory concurrency protection** using `lockForUpdate()`
- **Role-based access control** with `admin` and `operator` policies
- **Historical price snapshots** stored in order items
- **45 automated tests / 374 assertions**
- **OpenAPI 3 contract** with interactive Swagger UI
- **Automated OpenAPI contract validation**
- **GitHub Actions CI** on pushes and pull requests
- **Docker Compose environment** with persistent SQLite storage
- Clear separation through **Form Requests, API Resources, Policies, and Eloquent relationships**

## Tech Stack

| Area | Technology |
|---|---|
| Language | PHP 8.4 |
| Framework | Laravel 13 |
| Authentication | Laravel Sanctum |
| Authorization | Laravel Policies |
| Database | SQLite |
| ORM | Eloquent |
| API Documentation | OpenAPI 3, L5-Swagger, swagger-php, Swagger UI |
| Testing | PHPUnit / Laravel Feature Tests |
| Dependency Management | Composer |
| Containers | Docker, Docker Compose |
| CI | GitHub Actions |

## Quick Start with Docker

The fastest way to run the project is with Docker.

```bash
docker compose up -d --build
```

Check the container:

```bash
docker compose ps
```

When the application reports `healthy`:

- API: `http://127.0.0.1:8000`
- Swagger UI: `http://127.0.0.1:8000/api/documentation`

Run the complete test suite inside the container:

```bash
docker compose exec app php artisan test
```

Stop the environment:

```bash
docker compose down
```

The named Docker volume preserves the SQLite database and the development application key.

To intentionally remove persisted Docker data:

```bash
docker compose down -v
```

## Architecture Overview

```text
Client
  |
  v
Laravel API
  |
  +-- Sanctum Authentication
  |
  +-- Policies / Role Authorization
  |
  +-- Form Request Validation
  |
  +-- Controllers
  |     |
  |     +-- Database Transactions
  |     +-- Inventory Row Locks
  |
  +-- Eloquent Models
  |
  +-- API Resources
  |
  v
SQLite Database
```

### Domain Model

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

Main entities:

- **Customer** — customer information and order ownership
- **Category** — product classification
- **Product** — pricing, inventory, category, and active status
- **Order** — customer, lifecycle status, and calculated total
- **OrderItem** — product, quantity, historical unit price, and subtotal

## Engineering Highlights

### Transactional Order Processing

Order creation runs inside a database transaction.

For each requested item, the API:

1. validates the customer and item payload;
2. locks the product row for update;
3. verifies that the product is active;
4. verifies available inventory;
5. snapshots the current product price;
6. calculates the item subtotal;
7. reduces stock;
8. accumulates the order total.

The transaction is committed only when **every item** can be processed.

If a later item fails, previously processed changes are rolled back. This protects both the order records and inventory from partial updates.

### Inventory Protection

Inventory rules include:

- insufficient stock rejection;
- inactive product rejection;
- cumulative stock handling when the same product appears more than once;
- rollback when any item in a multi-item order fails;
- stock restoration when an eligible pending order is deleted.

`lockForUpdate()` is used while processing inventory to reduce the risk of conflicting stock updates.

### Historical Price Preservation

When an order is created, the current product price is copied to:

```text
order_items.unit_price
```

This preserves the original transaction value even if the product price changes later.

### Controlled Order Lifecycle

Only `pending` orders can be deleted.

Deleting a pending order restores its item quantities to inventory inside a database transaction.

Orders that already moved to another lifecycle state cannot be deleted through this operation.

## Authentication

Authentication uses Laravel Sanctum personal access tokens.

### Login

```http
POST /api/login
```

Example:

```json
{
  "email": "user@example.com",
  "password": "your-password"
}
```

A successful login returns the authenticated user and a personal access token.

Use the token in protected requests:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

### Current User

```http
GET /api/user
```

### Logout

```http
POST /api/logout
```

Logout revokes the **current access token**. Other valid tokens belonging to the same user remain available.

## Authorization

The application currently supports two roles:

- `admin` — manages master data and all supported order operations;
- `operator` — reads business resources and performs operational order work.

### Permission Matrix

| Resource / Action | Admin | Operator |
|---|:---:|:---:|
| List/view customers | Yes | Yes |
| Create/update/delete customers | Yes | No |
| List/view categories | Yes | Yes |
| Create/update/delete categories | Yes | No |
| List/view products | Yes | Yes |
| Create/update/delete products | Yes | No |
| List/view orders | Yes | Yes |
| Create orders | Yes | Yes |
| Update order status | Yes | Yes |
| Delete pending orders | Yes | No |

Unauthorized operations return `403 Forbidden`.

## API Endpoints

Except for login, business endpoints require authentication. Individual operations are also subject to role policies.

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/login` | Authenticate and issue an access token |
| GET | `/api/user` | Return the authenticated user |
| POST | `/api/logout` | Revoke the current access token |

### Customers

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/customers` | List customers |
| POST | `/api/customers` | Create a customer |
| GET | `/api/customers/{id}` | Show a customer |
| PUT/PATCH | `/api/customers/{id}` | Update a customer |
| DELETE | `/api/customers/{id}` | Delete a customer |

### Categories

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/categories` | List categories |
| POST | `/api/categories` | Create a category |
| GET | `/api/categories/{id}` | Show a category |
| PUT/PATCH | `/api/categories/{id}` | Update a category |
| DELETE | `/api/categories/{id}` | Delete a category |

### Products

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/products` | List products |
| POST | `/api/products` | Create a product |
| GET | `/api/products/{id}` | Show a product |
| PUT/PATCH | `/api/products/{id}` | Update a product |
| DELETE | `/api/products/{id}` | Delete a product |

### Orders

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/orders` | List orders |
| POST | `/api/orders` | Create an order |
| GET | `/api/orders/{id}` | Show an order |
| PUT/PATCH | `/api/orders/{id}` | Update order status |
| DELETE | `/api/orders/{id}` | Delete a pending order |

## Order Example

Both `admin` and `operator` users can create orders.

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

The API calculates the total from server-side product prices and reduces inventory only when the complete operation succeeds.

## OpenAPI / Swagger

The API contract is documented with OpenAPI 3 using L5-Swagger and swagger-php attributes.

The specification covers:

- authentication and Sanctum Bearer security;
- customers, categories, products, and orders;
- request and response schemas;
- pagination;
- role-sensitive operations;
- common `401`, `403`, `404`, `409`, and `422` responses;
- order and inventory business rules.

Generate the specification:

```bash
php artisan l5-swagger:generate
```

Swagger UI:

```text
http://127.0.0.1:8000/api/documentation
```

The generated JSON under `storage/api-docs` is intentionally excluded from version control. PHP definitions under `app/OpenApi` are the source of truth.

An automated test also regenerates and validates the OpenAPI contract.

## Automated Tests

Run locally:

```bash
php artisan test
```

Current suite:

```text
45 tests
374 assertions
```

Coverage includes:

- authentication and invalid credentials;
- protected route access;
- current-token logout behavior;
- admin/operator authorization;
- operator protection against pending-order deletion;
- customer, category, and product CRUD;
- validation and resource response structures;
- order creation and total calculation;
- inventory reduction;
- insufficient stock;
- inactive products;
- transaction rollback after a later item fails;
- cumulative stock handling for duplicate product lines;
- pending-order stock restoration;
- non-pending order deletion protection;
- OpenAPI generation and contract requirements.

## Continuous Integration

GitHub Actions automatically runs the test suite on pushes and pull requests to `main`.

The workflow validates:

1. repository checkout;
2. PHP environment;
3. Composer dependencies;
4. Laravel environment setup;
5. application key generation;
6. the complete automated test suite.

Because OpenAPI validation is part of the PHPUnit suite, CI also checks that the API contract can still be generated successfully.

## Local Installation

If you prefer to run the application without Docker:

```bash
git clone https://github.com/iuriabreugarcia/order-management-api.git
cd order-management-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan l5-swagger:generate
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000/api
```

Swagger UI:

```text
http://127.0.0.1:8000/api/documentation
```

### Demo Data

`php artisan migrate:fresh --seed` creates sample business data:

- 10 customers
- 3 categories
- 5 products

The demo seeder intentionally does not create an authentication user. Create a local user before testing authenticated endpoints.

## Project Structure

```text
app/
|-- Http/
|   |-- Controllers/Api/
|   |-- Requests/
|   `-- Resources/
|-- Models/
|-- OpenApi/
|   |-- Paths/
|   `-- Schemas/
`-- Policies/
config/
database/
routes/
tests/
|-- Feature/
`-- Unit/
docker/
.github/workflows/
.dockerignore
Dockerfile
docker-compose.yml
```

## What This Project Demonstrates

This project is intentionally designed as a backend engineering portfolio piece rather than a minimal CRUD example.

It demonstrates practical experience with:

- REST API design;
- authentication and authorization;
- domain and business-rule modeling;
- transactional consistency;
- inventory concurrency concerns;
- database relationships and constraints;
- validation and response transformation;
- API contract documentation;
- automated feature and edge-case testing;
- continuous integration;
- containerized development environments.

## Project Status

**Complete.**

The planned portfolio roadmap has been implemented:

- [x] Token-based API authentication
- [x] Form Request validation
- [x] API Resources
- [x] CRUD feature test coverage
- [x] Order and inventory business-rule tests
- [x] Continuous Integration with GitHub Actions
- [x] Authorization policies and roles
- [x] OpenAPI / Swagger documentation
- [x] Docker environment
- [x] Additional edge-case and authorization tests

## Author

**Iuri Abreu e Garcia**

Backend Development | Systems Analysis | Data Analysis
