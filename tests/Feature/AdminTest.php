<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_is_protected(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        
        $response = $this->actingAs($admin)->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_access_products_page(): void
    {
        $admin = User::factory()->create();
        
        $response = $this->actingAs($admin)->get('/admin/products');
        
        $response->assertStatus(200);
    }
}
