<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorizationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_view_business_resources(): void
    {
        Sanctum::actingAs(User::factory()->operator()->create());

        $customer = Customer::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->getJson('/api/customers')->assertOk();
        $this->getJson("/api/customers/{$customer->id}")->assertOk();
        $this->getJson('/api/categories')->assertOk();
        $this->getJson("/api/categories/{$category->id}")->assertOk();
        $this->getJson('/api/products')->assertOk();
        $this->getJson("/api/products/{$product->id}")->assertOk();
        $this->getJson('/api/orders')->assertOk();
    }

    public function test_operator_cannot_manage_master_data(): void
    {
        Sanctum::actingAs(User::factory()->operator()->create());

        $customer = Customer::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->postJson('/api/customers', [
            'name' => 'Forbidden Customer',
            'email' => 'forbidden-customer@example.com',
        ])->assertForbidden();

        $this->patchJson("/api/customers/{$customer->id}", [
            'name' => 'Forbidden Update',
        ])->assertForbidden();

        $this->deleteJson("/api/customers/{$customer->id}")
            ->assertForbidden();

        $this->postJson('/api/categories', [
            'name' => 'Forbidden Category',
        ])->assertForbidden();

        $this->patchJson("/api/categories/{$category->id}", [
            'name' => 'Forbidden Category Update',
        ])->assertForbidden();

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertForbidden();

        $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Forbidden Product',
            'price' => 10,
            'stock' => 5,
            'active' => true,
        ])->assertForbidden();

        $this->patchJson("/api/products/{$product->id}", [
            'name' => 'Forbidden Product Update',
        ])->assertForbidden();

        $this->deleteJson("/api/products/{$product->id}")
            ->assertForbidden();
    }

    public function test_operator_can_create_and_update_orders_but_cannot_delete_them(): void
    {
        Sanctum::actingAs(User::factory()->operator()->create());

        $customer = Customer::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 10,
            'stock' => 10,
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

        $this->patchJson("/api/orders/{$orderId}", [
            'status' => 'processing',
        ])
            ->assertOk()
            ->assertJsonPath('status', 'processing');

        $this->deleteJson("/api/orders/{$orderId}")
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'processing',
        ]);
    }

    public function test_admin_can_manage_master_data_and_delete_pending_orders(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $customerResponse = $this->postJson('/api/customers', [
            'name' => 'Admin Customer',
            'email' => 'admin-customer@example.com',
        ])->assertCreated();

        $categoryResponse = $this->postJson('/api/categories', [
            'name' => 'Admin Category',
        ])->assertCreated();

        $productResponse = $this->postJson('/api/products', [
            'category_id' => $categoryResponse->json('id'),
            'name' => 'Admin Product',
            'price' => 15,
            'stock' => 10,
            'active' => true,
        ])->assertCreated();

        $orderResponse = $this->postJson('/api/orders', [
            'customer_id' => $customerResponse->json('id'),
            'items' => [
                [
                    'product_id' => $productResponse->json('id'),
                    'quantity' => 2,
                ],
            ],
        ])->assertCreated();

        $this->deleteJson('/api/orders/'.$orderResponse->json('id'))
            ->assertNoContent();
    }
}
