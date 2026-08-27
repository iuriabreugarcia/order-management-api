<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

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
            ->assertJsonPath('items.0.quantity', 3)
            ->assertJsonPath('items.0.subtotal', '25.50');

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


