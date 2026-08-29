<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create()
        );
    }

    public function test_customer_can_be_created(): void
    {
        $response = $this->postJson('/api/customers', [
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'phone' => '71999999999',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'John Customer')
            ->assertJsonPath('email', 'john@example.com')
            ->assertJsonPath('phone', '71999999999');

        $this->assertDatabaseHas('customers', [
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'phone' => '71999999999',
        ]);
    }

    public function test_customers_can_be_listed(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_customer_can_be_shown(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Jane Customer',
            'email' => 'jane@example.com',
        ]);

        $response = $this->getJson("/api/customers/{$customer->id}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $customer->id)
            ->assertJsonPath('name', 'Jane Customer')
            ->assertJsonPath('email', 'jane@example.com');
    }

    public function test_customer_can_be_updated(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->putJson("/api/customers/{$customer->id}", [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '71888888888',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('name', 'New Name')
            ->assertJsonPath('email', 'new@example.com')
            ->assertJsonPath('phone', '71888888888');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '71888888888',
        ]);
    }

    public function test_customer_can_be_deleted_when_it_has_no_orders(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_customer_email_must_be_unique(): void
    {
        Customer::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->postJson('/api/customers', [
            'name' => 'Another Customer',
            'email' => 'duplicate@example.com',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}