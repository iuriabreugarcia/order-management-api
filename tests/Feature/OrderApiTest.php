<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create()
        );
    }

    public function test_order_is_created_and_stock_is_reduced(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'description' => '500ml',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('total', '25.50')
            ->assertJsonPath('customer.id', $customer->id)
            ->assertJsonPath('items.0.product_id', $product->id)
            ->assertJsonPath('items.0.quantity', 3)
            ->assertJsonPath('items.0.unit_price', '8.50')
            ->assertJsonPath('items.0.subtotal', '25.50')
            ->assertJsonPath('items.0.product.id', $product->id)
            ->assertJsonStructure([
                'id',
                'customer_id',
                'status',
                'total',
                'customer' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
                'items' => [
                    '*' => [
                        'id',
                        'product_id',
                        'quantity',
                        'unit_price',
                        'subtotal',
                        'product',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total' => 25.50,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 8.50,
            'subtotal' => 25.50,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 17,
        ]);
    }

    public function test_orders_can_be_listed(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ])->assertCreated();

        $response = $this->getJson('/api/orders');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customer.id', $customer->id)
            ->assertJsonPath('data.0.items.0.product.id', $product->id)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'status',
                        'total',
                        'customer',
                        'items',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_order_can_be_shown(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $orderResponse->assertCreated();

        $orderId = $orderResponse->json('id');

        $response = $this->getJson("/api/orders/{$orderId}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $orderId)
            ->assertJsonPath('customer_id', $customer->id)
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('total', '17.00')
            ->assertJsonPath('customer.id', $customer->id)
            ->assertJsonPath('items.0.product.id', $product->id)
            ->assertJsonPath('items.0.quantity', 2)
            ->assertJsonPath('items.0.subtotal', '17.00');
    }

    public function test_order_status_can_be_updated(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $orderResponse->assertCreated();

        $orderId = $orderResponse->json('id');

        $response = $this->patchJson("/api/orders/{$orderId}", [
            'status' => 'processing',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('id', $orderId)
            ->assertJsonPath('status', 'processing');

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'processing',
        ]);
    }

    public function test_order_is_rejected_when_stock_is_insufficient(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);
    }

    public function test_inactive_product_cannot_be_ordered(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => false,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 20,
        ]);
    }

    public function test_deleting_pending_order_restores_stock(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $orderResponse->assertCreated();

        $orderId = $orderResponse->json('id');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 17,
        ]);

        $response = $this->deleteJson("/api/orders/{$orderId}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('orders', [
            'id' => $orderId,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 20,
        ]);
    }

    public function test_non_pending_order_cannot_be_deleted(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
        ]);

        $category = Category::create([
            'name' => 'Beverages',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
            'price' => 8.50,
            'stock' => 20,
            'active' => true,
        ]);

        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $orderResponse->assertCreated();

        $orderId = $orderResponse->json('id');

        $this->patchJson("/api/orders/{$orderId}", [
            'status' => 'completed',
        ])->assertOk();

        $response = $this->deleteJson("/api/orders/{$orderId}");

        $response
            ->assertStatus(409)
            ->assertJson([
                'message' => 'Only pending orders can be deleted.',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 17,
        ]);
    }
}