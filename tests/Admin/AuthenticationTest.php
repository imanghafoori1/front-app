<?php

namespace Tests\Admin;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    #[Test]
    public function admin_cannot_login_with_invalid_credentials()
    {
        // arrange:
        User::factory()->create(['email' => 'admin@admin.com']);
        // act:
        $response = $this->post('login', [
            'email' => 'admin@admin.com',
            'password' => 'something',
        ]);
        // assert:
        $response->assertRedirect();
        $this->assertGuest();
    }

    #[Test]
    public function test_login_page_loads_successfully()
    {
        // act:
        $response = $this->get('login');
        // assert:
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_login_with_valid_credentials()
    {
        // arrange:
        $admin = User::factory()->create(['email' => 'admin@admin.com']);

        // act:
        $response = $this->post(route('login'), [
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        // assert:
        $response->assertRedirect(route('admin.products'));
        $this->assertAuthenticatedAs($admin);
    }

    #[Test]
    public function admin_can_logout()
    {
        // arrange:
        $admin = User::factory()->create();
        $this->actingAs($admin);

        // act:
        $response = $this->get('logout');

        // assert:
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function tearDown(): void
    {
        User::query()->delete();
        parent::tearDown();
    }
}
