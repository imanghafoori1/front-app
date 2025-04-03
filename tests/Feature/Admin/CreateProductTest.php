<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_see_form()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());

        // act:
        $response = $this->get('/admin/products/add');

        // assert:
        $response->assertOk();
    }

    #[Test]
    public function admin_can_add_product()
    {
        // arrange:
        Event::fake();
        $this->actingAs(User::factory()->createQuietly());
        Storage::fake('public');

        // act:
        $response = $this->post('/admin/products/add', [
            'name' => 'My Product',
            'price' => 456.4,
            'description' => 'sample Description',
            'image' => UploadedFile::fake()->image('prd-image.jpg'),
        ]);

        // assert:
        Event::assertDispatched('eloquent.creating: '.Product::class, 1);
        Event::assertDispatched('eloquent.created: '.Product::class, 1);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['name' => 'My Product']);
        $this->assertDatabaseCount('products', 1);
    }

    #[Test]
    public function admin_for_is_validated()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());
        Storage::fake('public');

        // act:
        $response = $this->post('/admin/products/add', [
            'name' => 'My',
            'price' => 456.4,
            'description' => 'sample Description',
            'image' => UploadedFile::fake()->image('prd-image.jpg'),
        ]);

        // assert:
        $response->assertRedirect();
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('products', 0);
    }

    #[Test]
    public function guest_can_not_see_form()
    {
        // act:
        $response = $this->get('/admin/products/add');

        // assert:
        $response->assertRedirect('/login');
    }
}
