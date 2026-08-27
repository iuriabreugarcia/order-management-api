# Order Management API

RESTful API for order management and inventory control, built with Laravel and PHP.

This project demonstrates the implementation of a transactional order workflow, including customers, product categories, inventory management, order processing, stock validation, and automated tests.

## Features

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

## Tech Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum
- Eloquent ORM
- SQLite
- PHPUnit
- Composer
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

### Main entities

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

### Transactional order processing

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

### Inventory protection

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

### Price history

The current product price is copied to `order_items.unit_price` when an order is created.

This preserves the original transaction value even if the product price changes later.

### Order deletion

Only orders with `pending` status can be deleted.

When a pending order is deleted, its quantities are returned to inventory inside a database transaction.

Orders that have already moved to another lifecycle status cannot be deleted through this operation.

## API Endpoints

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
| PATCH | `/api/orders/{id}` | Update order status |
| DELETE | `/api/orders/{id}` | Delete pending order |

## Creating an Order

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

This allows the API to be explored immediately after installation.

## Automated Tests

Run:

```bash
php artisan test
```

The feature test suite validates critical order behavior, including:

- successful order creation
- automatic inventory reduction
- insufficient stock rejection
- transaction rollback behavior
- stock restoration when a pending order is deleted
- protection against deleting non-pending orders

Current test suite:

```text
6 tests
28 assertions
```

## Project Structure

```text
app/
├── Http/
│   └── Controllers/
│       └── Api/
├── Models/
database/
├── factories/
├── migrations/
└── seeders/
routes/
└── api.php
tests/
└── Feature/
```

## Engineering Decisions

This project intentionally goes beyond a basic CRUD implementation.

Some of the technical decisions include:

- database transactions to preserve consistency
- `lockForUpdate()` during inventory processing
- historical price storage in order items
- foreign-key constraints
- model relationships through Eloquent
- controlled order lifecycle
- inventory restoration rules
- automated tests for critical business behavior

These decisions are intended to demonstrate backend development focused not only on endpoints, but also on data integrity and business rules.

## Roadmap

- API authentication and authorization
- Form Request classes
- API Resources
- expanded automated test coverage
- CI pipeline with GitHub Actions
- API documentation
- Docker environment

## Author

**Iuri Abreu e Garcia**

Backend Development • Systems Analysis • Data Analysis