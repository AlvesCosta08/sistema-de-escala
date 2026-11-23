<?php

namespace Tests\Feature\Admin;

use App\Models\Escala;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarEscalasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesSeeder::class);
    }

    private function criarAdmin(): User
    {
        $admin = User::factory()->create(['cargo' => 'admin']);
        $admin->assignRole('admin');
        return $admin;
    }

    public function test_colaborador_nao_acessa_gerenciamento_de_escalas()
    {
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $porteiro->assignRole('porteiro');

        // CORRIGIDO: Mudou de escalas.index para escalas.gerenciar
        $response = $this->actingAs($porteiro)->get(route('admin.escalas.gerenciar'));
        $response->assertStatus(403);
    }

    public function test_admin_acessa_lista_de_escalas()
    {
        $admin = $this->criarAdmin();
        Escala::create([
            'mes_referencia' => '2026-11',
            'status' => 'rascunho',
            'created_by' => $admin->id
        ]);

        // CORRIGIDO: Mudou de escalas.index para escalas.gerenciar
        $response = $this->actingAs($admin)->get(route('admin.escalas.gerenciar'));
        
        $response->assertOk();
        $response->assertSee('Novembro/2026');
    }

    public function test_admin_cria_nova_escala()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->post(route('admin.escalas.store'), [
            'mes_referencia' => '2026-12',
        ]);

        // CORRIGIDO: Mudou de escalas.index para escalas.gerenciar
        $response->assertRedirect(route('admin.escalas.gerenciar'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('escalas', [
            'mes_referencia' => '2026-12',
            'status' => 'rascunho',
            'created_by' => $admin->id
        ]);
    }

    public function test_admin_publica_uma_escala()
    {
        $admin = $this->criarAdmin();
        $escala = Escala::create([
            'mes_referencia' => '2026-11',
            'status' => 'rascunho',
            'created_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.escalas.publicar', $escala));

        // CORRIGIDO: Mudou de escalas.index para escalas.gerenciar
        $response->assertRedirect(route('admin.escalas.gerenciar'));
        $response->assertSessionHas('success');
        $this->assertEquals('publicada', $escala->fresh()->status);
    }

    public function test_admin_nao_cria_escala_duplicada()
    {
        $admin = $this->criarAdmin();
        Escala::create([
            'mes_referencia' => '2026-11',
            'status' => 'rascunho',
            'created_by' => $admin->id
        ]);

        $response = $this->actingAs($admin)->post(route('admin.escalas.store'), [
            'mes_referencia' => '2026-11',
        ]);

        $response->assertSessionHasErrors('mes_referencia');
    }
}