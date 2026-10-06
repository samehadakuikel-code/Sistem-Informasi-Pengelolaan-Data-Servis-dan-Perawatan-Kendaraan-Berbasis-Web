<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guests_see_the_intro_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertSee('Rawat.');
    }

    public function test_login_page_is_available(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Selamat datang.');
    }
}
