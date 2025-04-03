<?php

namespace Tests;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        User::query()->delete();
        Product::query()->delete();

        parent::tearDown();
    }
}
