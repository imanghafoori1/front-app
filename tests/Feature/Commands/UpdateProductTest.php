<?php

namespace Tests\Feature\Commands;

use App\Jobs\SendPriceChangeNotification;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Bus::fake();
        Event::fake();
    }

    #[Test]
    public function update_product_command_with_valid_inputs()
    {
        $product = Product::factory()->createQuietly([
            'name' => 'old Product name',
            'price' => 220.00,
            'description' => 'initial description Of Product.',
        ]);

        $this->artisan('product:update', [
            'id' => $product->id,
            '--name' => 'new Product name',
            '--price' => 240.00,
            '--description' => 'old description',
        ])->assertOk()
            ->assertExitCode(0)
            ->expectsOutputToContain('Price changed from 220 to 240.')
            ->expectsOutputToContain('Price change notification dispatched to ')
            ->expectsOutputToContain('Product updated successfully.');

        Bus::assertDispatched(SendPriceChangeNotification::class);
        Event::assertDispatched('eloquent.saving: '.Product::class);
        Event::assertDispatched('eloquent.saved: '.Product::class);
        Event::assertDispatched('eloquent.updated: '.Product::class);

        $updatedProduct = Product::query()->find($product->id);
        $this->assertEquals('new Product name', $updatedProduct->name);
        $this->assertEquals(240.00, $updatedProduct->price);
        $this->assertEquals('old description', $updatedProduct->description);
    }

    #[Test]
    public function update_product_command_with_invalid_inputs()
    {
        $product = Product::factory()->createQuietly([
            'name' => 'initial Product name',
            'price' => 220.00,
            'description' => 'initial description Of Product.',
        ]);

        $this->artisan('product:update', [
            'id' => $product->id,
            '--name' => ' ',
            '--price' => 240.00,
            '--description' => 'new description',
        ])->assertOk()
            ->assertExitCode(1)
            ->expectsOutputToContain('Name cannot be empty.');

        $this->artisan('product:update', [
            'id' => $product->id,
            '--name' => 'a',
            '--price' => 240.00,
            '--description' => 'new description',
        ])->assertOk()
            ->assertExitCode(1)
            ->expectsOutputToContain('Name must be at least 3 characters long.');

        Bus::assertNotDispatched(SendPriceChangeNotification::class);
        Event::assertNotDispatched('eloquent.saving: '.Product::class);
        Event::assertNotDispatched('eloquent.saved: '.Product::class);
        Event::assertNotDispatched('eloquent.updated: '.Product::class);

        $updatedProduct = Product::query()->find($product->id);
        $this->assertEquals('initial Product name', $updatedProduct->name);
        $this->assertEquals(220.00, $updatedProduct->price);
        $this->assertEquals('initial description Of Product.', $updatedProduct->description);
    }

    #[Test]
    public function update_product_command_with_no_changes()
    {
        $product = Product::factory()->createQuietly([
            'name' => 'initial Product name',
            'price' => 400.00,
            'description' => 'initial description Of Product.',
        ]);

        $this->artisan('product:update', [
            'id' => $product->id,
        ])->assertOk()
            ->assertExitCode(0)
            ->doesntExpectOutputToContain('Product updated successfully.')
            ->expectsOutputToContain('No changes provided. Product remains unchanged.');

        Bus::assertNotDispatched(SendPriceChangeNotification::class);
        Event::assertNotDispatched('eloquent.saving: '.Product::class);
        Event::assertNotDispatched('eloquent.saved: '.Product::class);
        Event::assertNotDispatched('eloquent.updated: '.Product::class);

        $updatedProduct = Product::query()->find($product->id);
        $this->assertEquals('initial Product name', $updatedProduct->name);
        $this->assertEquals(400.00, $updatedProduct->price);
        $this->assertEquals('initial description Of Product.', $updatedProduct->description);
    }

    #[Test]
    public function update_product_command_with_non_existing_id()
    {
        $product = Product::factory()->createQuietly([
            'name' => 'initial Product name 3',
            'price' => 220.00,
            'description' => 'initial description Of Product 3.',
        ]);

        $this->artisan('product:update', [
            'id' => $product->id + 1,
            '--name' => 'new name',
            '--price' => 250.00,
            '--description' => 'new description',
        ])->assertExitCode(1)->expectsOutputToContain('Product not found with id: ');

        Bus::assertNotDispatched(SendPriceChangeNotification::class);
        Event::assertNotDispatched('eloquent.saving: '.Product::class);
        Event::assertNotDispatched('eloquent.saved: '.Product::class);
        Event::assertNotDispatched('eloquent.updated: '.Product::class);

        $updatedProduct = Product::query()->find($product->id);
        $this->assertEquals('initial Product name 3', $updatedProduct->name);
        $this->assertEquals(220.00, $updatedProduct->price);
        $this->assertEquals('initial description Of Product 3.', $updatedProduct->description);
    }
}
