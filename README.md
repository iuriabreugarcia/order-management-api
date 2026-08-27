# Order Management API

REST API for order, product and inventory management built with Laravel.

## About the project

This project demonstrates the backend architecture of a small business management system.

The application manages:

* Customers
* Products
* Categories
* Inventory
* Orders
* Order items
* Order status

The project was inspired by real-world business requirements involving order processing, sales and inventory management.

## Tech Stack

* PHP
* Laravel
* MySQL / PostgreSQL
* REST API
* Laravel Eloquent ORM
* Git
* Docker

## Architecture

The application follows a layered and maintainable structure using Laravel conventions and principles such as:

* MVC
* Object-Oriented Programming
* RESTful design
* Data validation
* Separation of responsibilities

## Main Features

### Customers

Create, update, list and manage customers.

### Products

Manage products, categories, prices and available inventory.

### Orders

Create orders containing multiple products and quantities.

The application calculates order totals and manages the relationship between orders and products.

### Inventory

Inventory quantities are updated according to business operations.

### REST API

The project exposes REST endpoints for integration with web, mobile or external applications.

## Example Endpoints

`GET /api/products`

`POST /api/products`

`GET /api/customers`

`POST /api/customers`

`GET /api/orders`

`POST /api/orders`

`GET /api/orders/{id}`

`PATCH /api/orders/{id}/status`

## Database

Main entities:

* users
* customers
* categories
* products
* orders
* order_items

Relationship example:

Customer → Orders → Order Items → Products

## Installation

Clone the repository:

```bash
git clone https://github.com/iuriabreugarcia/order-management-api.git
```

Enter the project directory:

```bash
cd order-management-api
```

Install dependencies:

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

Configure the database in `.env` and run:

```bash
php artisan migrate --seed
```

Start the application:

```bash
php artisan serve
```

## Purpose

This repository is part of my professional software development portfolio.

It demonstrates practical knowledge in backend development, relational databases, REST APIs and business-oriented software architecture.

## Author

**Iuri Abreu e Garcia**

Systems Analyst & Full Stack Developer

Portfolio: bacuridigital.com

LinkedIn: linkedin.com/in/iuri-garcia-246858237
