<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create()
        );
    }

    public function test_product_can_be_created(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Notebook',
            'description' => 'Business notebook',
            'price' => 3500.90,
            'stock' => 10,
            'active' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Notebook')
            ->assertJsonPath('description', 'Business notebook')
            ->assertJsonPath('price', '3500.90')
            ->assertJsonPath('stock', 10)
            ->assertJsonPath('active', true)
            ->assertJsonPath('category.id', $category->id);

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'Notebook',
            'stock' => 10,
            'active' => true,
        ]);
    }

    public function test_products_can_be_listed(): void
    {
        $category = Category::factory()->create();

        Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'category_id',
                        'name',
                        'description',
                        'price',
                        'stock',
                        'active',
                        'category',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_product_can_be_shown_with_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Electronics',
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Monitor',
            'price' => 899.90,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $product->id)
            ->assertJsonPath('name', 'Monitor')
            ->assertJsonPath('price', '899.90')
            ->assertJsonPath('stock', 5)
            ->assertJsonPath('active', true)
            ->assertJsonPath('category.id', $category->id)
            ->assertJsonPath('category.name', 'Electronics');
    }

    public function test_product_can_be_updated(): void
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Old Product',
            'price' => 100.00,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'category_id' => $category->id,
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 150.50,
            'stock' => 20,
            'active' => false,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('name', 'Updated Product')
            ->assertJsonPath('description', 'Updated description')
            ->assertJsonPath('price', '150.50')
            ->assertJsonPath('stock', 20)
            ->assertJsonPath('active', false);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'stock' => 20,
            'active' => false,
        ]);
    }

    public function test_product_can_be_deleted_when_it_has_no_order_items(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_requires_valid_category(): void
    {
        $response = $this->postJson('/api/products', [
            'category_id' => 999999,
            'name' => 'Invalid Product',
            'price' => 10,
            'stock' => 1,
            'active' => true,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category_id');
    }

    public function test_product_price_cannot_be_negative(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Invalid Product',
            'price' => -10,
            'stock' => 1,
            'active' => true,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('price');
    }

    public function test_product_stock_cannot_be_negative(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'category_id' => $category->id,
            'name' => 'Invalid Product',
            'price' => 10,
            'stock' => -1,
            'active' => true,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('stock');
    }
}