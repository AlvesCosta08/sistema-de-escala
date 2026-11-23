<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarAlocacoesTest extends TestCase
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

    public function test_colaborador_nao_acessa_gerenciamento_de_alocacoes()
    {
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $porteiro->assignRole('porteiro');
        $escala = Escala::create(['mes_referencia' => '2026-11', 'status' => 'rascunho', 'created_by' => $porteiro->id]);

        $response = $this->actingAs($porteiro)->get(route('admin.escalas.alocacoes.index', $escala));
        $response->assertStatus(403);
    }

    public function test_admin_acessa_tela_de_alocacao()
    {
        $admin = $this->criarAdmin();
        $escala = Escala::create(['mes_referencia' => '2026-11', 'status' => 'rascunho', 'created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('admin.escalas.alocacoes.index', $escala));
        $response->assertOk();
    }

    public function test_admin_cria_uma_alocacao()
    {
        $admin = $this->criarAdmin();
        $escala = Escala::create(['mes_referencia' => '2026-11', 'status' => 'rascunho', 'created_by' => $admin->id]);
        
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $posto = Posto::create(['nome' => 'Portaria A']);
        $turno = Turno::create(['nome' => 'Diurno', 'hora_inicio' => '07:00', 'hora_fim' => '19:00', 'tolerancia_atraso_min' => 10, 'intervalo_min' => 60]);

        $dados = [
            'user_id' => $porteiro->id,
            'posto_id' => $posto->id,
            'turno_id' => $turno->id,
            'data' => '2026-11-15',
        ];

        $response = $this->actingAs($admin)->post(route('admin.escalas.alocacoes.store', $escala), $dados);

        $response->assertRedirect(route('admin.escalas.alocacoes.index', $escala));
        $response->assertSessionHas('success');
        
        // CORREÇÃO AQUI: de 'alocacaos' para 'alocacoes'
        $this->assertDatabaseHas('alocacoes', [
            'escala_id' => $escala->id,
            'user_id' => $porteiro->id,
        ]);
    }

    public function test_admin_nao_pode_duplicar_alocacao_no_mesmo_dia_turno()
    {
        $admin = $this->criarAdmin();
        $escala = Escala::create(['mes_referencia' => '2026-11', 'status' => 'rascunho', 'created_by' => $admin->id]);
        
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $posto = Posto::create(['nome' => 'Portaria A']);
        $turno = Turno::create(['nome' => 'Diurno', 'hora_inicio' => '07:00', 'hora_fim' => '19:00', 'tolerancia_atraso_min' => 10, 'intervalo_min' => 60]);

        Alocacao::create([
            'escala_id' => $escala->id,
            'user_id' => $porteiro->id,
            'posto_id' => $posto->id,
            'turno_id' => $turno->id,
            'data' => '2026-11-15',
            'status' => 'confirmado'
        ]);

        $response = $this->actingAs($admin)->post(route('admin.escalas.alocacoes.store', $escala), [
            'user_id' => $porteiro->id,
            'posto_id' => $posto->id,
            'turno_id' => $turno->id,
            'data' => '2026-11-15',
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertEquals(1, Alocacao::count());
    }
}