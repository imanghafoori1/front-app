<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReadProductsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_not_see_list_products()
    {
        Product::factory()->createQuietly();

        // act:
        $response = $this->get('/admin/products');

        // assert:
        $response->assertRedirect('/login');
    }

    #[Test]
    public function products_page_loads_correctly()
    {
        // arrange:
        $this->actingAs(User::factory()->createQuietly());

        $products = Product::factory()->count(3)->createQuietly();

        // act:
        $response = $this->get('/admin/products');

        // assert:
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee($products->first()->name);
        $response->assertSee($products->first()->price);
        $response->assertSee($products->last()->name);
        $response->assertSee($products->last()->price);
    }
}
