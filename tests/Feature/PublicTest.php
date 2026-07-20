<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTest extends TestCase
{
    use RefreshDatabase;
    public function test_home_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_product_index_page_returns_a_successful_response(): void
    {
        $response = $this->get('/product');
        $response->assertStatus(200);
    }

    public function test_cart_page_returns_a_successful_response(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
    }

    public function test_checkout_page_redirects_if_cart_empty(): void
    {
        $response = $this->get('/checkout');
        $response->assertStatus(302);
        $response->assertRedirect('/cart');
    }

    public function test_about_page_returns_a_successful_response(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }
}
