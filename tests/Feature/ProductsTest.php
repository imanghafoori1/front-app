<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ExchangeRateService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    #[Test]
    public function product_page_show_single_product()
    {
        app()->bind(ExchangeRateService::class, function () {
            return new class {
                public function getRate()
                {
                    return 0.95;
                }
            };
        });

        $product1 = Product::factory()->createQuietly();
        $product2 = Product::factory()->createQuietly();

        // act:
        $response = $this->get('/');

        // assert:
        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertSee($product1->price);

        // act:
        $response = $this->get("/products/{$product2->getKey()}");

        // assert:
        $response->assertStatus(200);
        $response->assertSee($product2->name);
        $response->assertSee($product2->price);
    }

    #[Test]
    public function product_page_loads_correctly()
    {
        app()->bind(ExchangeRateService::class, function () {
            return new class {
                public function getRate()
                {
                    return 0.65;
                }
            };
        });

        $product1 = Product::factory()->createQuietly(['price' => 1250]);
        $product2 = Product::factory()->createQuietly(['price' => 4570]);

        // act:
        $response = $this->get("/products/{$product1->getKey()}");

        // assert:
        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertSee('$1,250.00');
        $response->assertSee('€812.50');
        $response->assertSee($product1->description);

        // act:
        $response = $this->get("/products/{$product2->getKey()}");

        // assert:
        $response->assertStatus(200);
        $response->assertSee($product2->name);
        $response->assertSee('$4,570.00');
        $response->assertSee($product2->description);
        $response->assertSee('Exchange Rate: 1 USD = 0.6500 EUR');
    }

    #[Test]
    public function product_page_show_invalid_product_id()
    {
        // act:
        $response = $this->get('/products/567');

        // assert:
        $response->assertStatus(404);
    }
}
