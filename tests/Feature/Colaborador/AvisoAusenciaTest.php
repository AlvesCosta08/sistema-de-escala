<?php

namespace Tests\Feature\Colaborador;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AvisoAusenciaTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function porteiroComAlocacao(array $alocacao = []): array
    {
        $u = $this->porteiro();
        $a = Alocacao::factory()->create(array_merge([
            'user_id' => $u->id,
            'data' => now()->addDays(3)->toDateString(),
            'status' => 'confirmado',
        ], $alocacao));
        return [$u, $a];
    }

    public function test_porteiro_pode_registrar_ausencia(): void
    {
        [$porteiro, $alocacao] = $this->porteiroComAlocacao();

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), [
                'motivo' => 'imprevisto',
                'observacao' => 'Consulta médica',
            ])
            ->assertRedirect(route('minha-escala'));

        $this->assertDatabaseHas('aviso_ausencias', [
            'alocacao_id' => $alocacao->id,
            'user_id' => $porteiro->id,
            'motivo' => 'imprevisto',
            'status' => 'pendente',
        ]);

        $this->assertSame('pendente', $alocacao->fresh()->status);
    }

    public function test_aviso_com_menos_de_24h_e_marcado_como_urgente(): void
    {
        [$porteiro, $alocacao] = $this->porteiroComAlocacao([
            'data' => now()->addHours(6)->toDateString(),
        ]);

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), ['motivo' => 'imprevisto']);

        $this->assertTrue(AvisoAusencia::first()->urgente);
    }

    public function test_aviso_com_mais_de_24h_nao_e_urgente(): void
    {
        [$porteiro, $alocacao] = $this->porteiroComAlocacao([
            'data' => now()->addDays(5)->toDateString(),
        ]);

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), ['motivo' => 'imprevisto']);

        $this->assertFalse(AvisoAusencia::first()->urgente);
    }

    public function test_nao_pode_marcar_ausencia_em_alocacao_de_outro(): void
    {
        [, $alocacao] = $this->porteiroComAlocacao();
        $outro = $this->porteiro();

        $this->actingAs($outro)
            ->post(route('ausencia.store', $alocacao), ['motivo' => 'imprevisto'])
            ->assertForbidden();

        $this->assertDatabaseCount('aviso_ausencias', 0);
    }

    public function test_motivo_invalido_e_rejeitado(): void
    {
        [$porteiro, $alocacao] = $this->porteiroComAlocacao();

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), ['motivo' => 'inventado'])
            ->assertSessionHasErrors('motivo');

        $this->assertDatabaseCount('aviso_ausencias', 0);
    }

    public function test_apenas_confirmado_aceita_ausencia(): void
    {
        [$porteiro, $alocacao] = $this->porteiroComAlocacao(['status' => 'substituida']);

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), ['motivo' => 'imprevisto'])
            ->assertForbidden();
    }
}