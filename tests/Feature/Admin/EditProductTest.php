<?php

namespace Tests\Feature\Admin;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_not_see_form()
    {
        $product = Product::factory()->createQuietly();

        // act:
        $response = $this->get("/admin/products/edit/$product->id");

        // assert:
        $response->assertRedirect('/login');
    }

    #[Test]
    public function admin_can_open_edit_form()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());
        $product = Product::factory()->createQuietly();

        // act:
        $response = $this->get("/admin/products/edit/$product->id");

        // assert:
        $response->assertStatus(200);
        $response->assertViewHas('product', $product);
    }

    #[Test]
    public function admin_can_not_open_edit_form_for_invalid_id()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());

        // act:
        $response = $this->get('/admin/products/edit/434');

        // assert:
        $response->assertStatus(404);
    }

    #[Test]
    public function admin_form_data_is_validated()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());
        $product1 = Product::factory()->createQuietly();

        // act:
        $response = $this->post("/admin/products/edit/$product1->id", [
            'name' => 'ne',
            'price' => 134.59,
            'description' => 'new Description',
        ]);

        // assert:
        $response->assertSessionHasErrors('name');
        $response->assertRedirect();
        // the record is affected in the DB:
        $this->assertDatabaseHas('products', [
            'name' => $product1->name,
            'price' => $product1->price,
            'description' => $product1->description,
        ]);
    }

    #[Test]
    public function admin_can_update_product()
    {
        // arrange:
        Bus::fake();
        Event::fake();
        $this->actingAs(User::factory()->createQuietly());
        $product1 = Product::factory()->createQuietly();
        $product2 = Product::factory()->createQuietly();

        // act:
        $response = $this->post("/admin/products/edit/$product1->id", [
            'name' => 'new name of Product',
            'price' => 134.59,
            'description' => 'new Description',
        ]);

        Event::assertDispatched('eloquent.updating: '.Product::class, 1);
        Event::assertDispatched('eloquent.updated: '.Product::class, 1);

        // assert:
        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'name' => 'new name of Product',
            'price' => 134.59,
            'description' => 'new Description',
        ]);
        // check other DB records are not affected:
        $this->assertDatabaseHas('products', [
            'name' => $product2->name,
            'price' => $product2->price,
            'description' => $product2->description,
        ]);
        // nothing is deleted or inserted as a side effect:
        $this->assertDatabaseCount('products', 2);
        Bus::assertDispatched(SendPriceChangeNotification::class);
    }

    #[Test]
    public function admin_can_update_product_with_same_price()
    {
        // arrange:
        Event::fake();
        Bus::fake();

        $this->actingAs(User::factory()->createQuietly());
        $product1 = Product::factory()->createQuietly(['price' => 134.59]);

        // act:
        $response = $this->post("/admin/products/edit/$product1->id", [
            'name' => 'new name of Product',
            'price' => 134.59,
            'description' => 'new Description',
        ]);

        // assert:
        Event::assertDispatched('eloquent.updated: '.Product::class, 1);
        Bus::assertNotDispatched(SendPriceChangeNotification::class);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'name' => 'new name of Product',
            'price' => 134.59,
            'description' => 'new Description',
        ]);
    }
}
