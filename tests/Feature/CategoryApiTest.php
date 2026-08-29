<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create()
        );
    }

    public function test_category_can_be_created(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Electronics',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Electronics');

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
        ]);
    }

    public function test_categories_can_be_listed(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_category_can_be_shown_with_products(): void
    {
        $category = Category::factory()->create([
            'name' => 'Beverages',
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Orange Juice',
        ]);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $category->id)
            ->assertJsonPath('name', 'Beverages')
            ->assertJsonPath('products.0.id', $product->id)
            ->assertJsonPath('products.0.name', 'Orange Juice');
    }

    public function test_category_can_be_updated(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old Category',
        ]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'New Category',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('id', $category->id)
            ->assertJsonPath('name', 'New Category');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Category',
        ]);
    }

    public function test_category_can_be_deleted_when_it_has_no_products(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_cannot_be_deleted_when_it_has_products(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response
            ->assertStatus(409)
            ->assertJsonPath(
                'message',
                'Category cannot be deleted because it has associated products.'
            );

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create([
            'name' => 'Beverages',
        ]);

        $response = $this->postJson('/api/categories', [
            'name' => 'Beverages',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }
}