# Order Management API

[![Tests](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml/badge.svg)](https://github.com/iuriabreugarcia/order-management-api/actions/workflows/tests.yml)

RESTful API for order management and inventory control, built with Laravel and PHP.

This project demonstrates the implementation of a transactional order workflow with token-based authentication, including customers, product categories, inventory management, order processing, stock validation, database consistency, and automated tests.

## Features

- Token-based API authentication with Laravel Sanctum
- Protected business endpoints
- Login and logout with personal access tokens
- Customer management
- Product category management
- Product and inventory management
- Order creation with multiple items
- Automatic order total calculation
- Automatic stock reduction
- Insufficient stock validation
- Database transactions for order processing
- Row locking during inventory updates
- Stock restoration when pending orders are deleted
- Order lifecycle control
- Pagination
- Request validation
- Eloquent relationships
- Demo database seeding
- Automated feature tests
- Continuous Integration with GitHub Actions

## Tech Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum
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
  "password": "password"
}
```

A successful login returns the authenticated user and a personal access token:

```json
{
  "user": {
    "id": 1,
    "name": "Example User",
    "email": "user@example.com"
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

## API Endpoints

Except for `/api/login`, the endpoints below require Sanctum authentication.

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

Authentication is required.

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

Example result:

```json
{
  "customer_id": 1,
  "status": "pending",
  "total": "25.50",
  "items": [
    {
      "product_id": 1,
      "quantity": 3,
      "unit_price": "8.50",
      "subtotal": "25.50"
    }
  ]
}
```

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

Start the development server:

```bash
php artisan serve
```

The API will be available by default at:

```text
http://127.0.0.1:8000/api
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

## Automated Tests

Run:

```bash
php artisan test
```

The automated test suite validates critical authentication and order-management behavior, including:

- login with valid credentials
- rejection of invalid credentials
- protection of authenticated endpoints
- authenticated access using Sanctum tokens
- token revocation during logout
- successful order creation
- automatic inventory reduction
- insufficient stock rejection
- transaction rollback behavior
- stock restoration when a pending order is deleted
- protection against deleting non-pending orders

Current test suite:

```text
11 tests
44 assertions
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

The status badge at the top of this README reflects the current CI result.

## Project Structure

```text
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── AuthController.php
│           ├── CategoryController.php
│           ├── CustomerController.php
│           ├── OrderController.php
│           └── ProductController.php
├── Models/
database/
├── factories/
├── migrations/
└── seeders/
routes/
└── api.php
tests/
├── Feature/
│   ├── AuthApiTest.php
│   └── OrderApiTest.php
└── Unit/
.github/
└── workflows/
    └── tests.yml
```

## Engineering Decisions

This project intentionally goes beyond a basic CRUD implementation.

Some of the technical decisions include:

- Laravel Sanctum personal access tokens for API authentication
- middleware protection for business endpoints
- token revocation during logout
- database transactions to preserve consistency
- `lockForUpdate()` during inventory processing
- historical price storage in order items
- foreign-key constraints
- model relationships through Eloquent
- controlled order lifecycle
- inventory restoration rules
- automated tests for authentication and critical business behavior
- continuous integration with GitHub Actions

These decisions are intended to demonstrate backend development focused not only on endpoints, but also on authentication, data integrity, concurrency, business rules, and automated quality assurance.

## Roadmap

- Authorization policies and roles
- Form Request classes
- API Resources
- Expanded automated test coverage
- OpenAPI / Swagger documentation
- Docker environment

## Author

**Iuri Abreu e Garcia**

Backend Development • Systems Analysis • Data Analysis