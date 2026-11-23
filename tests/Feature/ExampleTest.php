<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_dashboard_from_root(): void
    {
        // A rota raiz agora deve redirecionar (302) para o dashboard (que pedirá login se não estiver logado)
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
    }
}