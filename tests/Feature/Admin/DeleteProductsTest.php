<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteProductsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_delete_product()
    {
        // arrange:
        Event::fake();
        $this->actingAs(User::factory()->createQuietly());
        $product1 = Product::factory()->createQuietly();
        $product2 = Product::factory()->createQuietly();
        $product3 = Product::factory()->createQuietly();

        // act:
        $response = $this->get("/admin/products/delete/$product1->id");

        // assert:
        $response->assertRedirect('/admin/products');
        $response->assertSessionHas('success', 'Product deleted successfully');

        Event::assertDispatched('eloquent.deleting: '.Product::class, 1);
        Event::assertDispatched('eloquent.deleted: '.Product::class, 1);

        $this->assertDatabaseMissing('products', ['id' => $product1->id]);
        $this->assertDatabaseHas('products', ['id' => $product2->id]);
        $this->assertDatabaseHas('products', ['id' => $product3->id]);
    }

    #[Test]
    public function guest_can_not_delete_product()
    {
        // arrange:
        Event::fake();
        $product1 = Product::factory()->createQuietly();
        $product2 = Product::factory()->createQuietly();
        $product3 = Product::factory()->createQuietly();

        // act:
        $response = $this->get("/admin/products/delete/$product1->id");

        // assert:
        $response->assertRedirect('/login');
        $response->assertSessionMissing('success');

        Event::assertNotDispatched('eloquent.deleting: '.Product::class, 1);
        Event::assertNotDispatched('eloquent.deleted: '.Product::class, 1);

        $this->assertDatabaseHas('products', ['id' => $product1->id]);
        $this->assertDatabaseHas('products', ['id' => $product2->id]);
        $this->assertDatabaseHas('products', ['id' => $product3->id]);
    }

    #[Test]
    public function admin_deletes_non_existing_product()
    {
        // arrange:
        Event::fake();
        $this->actingAs(User::factory()->createQuietly());

        // act:
        $response = $this->get('/admin/products/delete/332');

        // assert:
        $response->assertStatus(404);
    }
}
