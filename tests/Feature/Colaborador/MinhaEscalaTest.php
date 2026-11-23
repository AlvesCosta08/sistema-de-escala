<?php

namespace Tests\Feature\Colaborador;

use App\Models\Alocacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class MinhaEscalaTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_porteiro_ve_apenas_suas_alocacoes(): void
    {
        $porteiro = $this->porteiro();
        $outro = $this->porteiro();

        $minha = Alocacao::factory()->create(['user_id' => $porteiro->id]);
        Alocacao::factory()->create(['user_id' => $outro->id]);

        $this->actingAs($porteiro)
            ->get(route('minha-escala'))
            ->assertOk()
            ->assertSee($minha->posto->nome)
            ->assertDontSee($outro->name);
    }

    public function test_admin_nao_acessa_minha_escala(): void
    {
        $this->actingAs($this->admin())
            ->get(route('minha-escala'))
            ->assertForbidden();
    }

    public function test_colaborador_nao_ve_botao_ausencia_em_alocacao_ja_pendente(): void
    {
        $porteiro = $this->porteiro();
        Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'status' => 'pendente',
        ]);

        $this->actingAs($porteiro)
            ->get(route('minha-escala'))
            ->assertOk()
            ->assertDontSee('Não poderei comparecer');
    }
}