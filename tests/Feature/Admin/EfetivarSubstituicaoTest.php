<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class EfetivarSubstituicaoTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Notification::fake();
    }

    private function avisoPendente(array $alocacaoAttrs = []): array
    {
        $solicitante = $this->porteiro();

        $alocacao = Alocacao::factory()->create(array_merge([
            'user_id' => $solicitante->id,
            'data' => now()->addDays(2)->toDateString(),
            'status' => 'pendente',
        ], $alocacaoAttrs));

        $aviso = AvisoAusencia::factory()->create([
            'alocacao_id' => $alocacao->id,
            'user_id' => $solicitante->id,
            'status' => 'pendente',
        ]);

        return [$aviso, $alocacao, $solicitante];
    }

    public function test_admin_aloca_substituto_e_gera_nova_alocacao(): void
    {
        [$aviso, $alocacaoOriginal] = $this->avisoPendente();
        $substituto = $this->porteiro();

        $this->actingAs($this->admin())
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $substituto->id,
            ])
            ->assertRedirect(route('admin.ausencias.index'));

        $this->assertSame('substituida', $alocacaoOriginal->fresh()->status);

        $nova = Alocacao::query()
            ->where('user_id', $substituto->id)
            ->whereDate('data', $alocacaoOriginal->data->toDateString())
            ->where('turno_id', $alocacaoOriginal->turno_id)
            ->where('posto_id', $alocacaoOriginal->posto_id)
            ->where('escala_id', $alocacaoOriginal->escala_id)
            ->where('status', 'confirmado')
            ->where('substitui_alocacao_id', $alocacaoOriginal->id)
            ->first();

        $this->assertNotNull($nova, 'Nova alocação do substituto não foi criada.');
        $this->assertSame(
            $alocacaoOriginal->data->toDateString(),
            $nova->data->toDateString(),
        );

        $aviso->refresh();
        $this->assertSame('substituto_alocado', $aviso->status);
        $this->assertSame($substituto->id, $aviso->substituto_user_id);
        $this->assertNotNull($aviso->resolvido_por);
        $this->assertNotNull($aviso->resolvido_em);
    }

    public function test_admin_rejeita_aviso_e_alocacao_volta_para_confirmado(): void
    {
        [$aviso, $alocacaoOriginal] = $this->avisoPendente();

        $this->actingAs($this->admin())
            ->post(route('admin.ausencias.rejeitar', $aviso), [
                'justificativa' => 'Colaborador informou que foi engano.',
            ])
            ->assertRedirect(route('admin.ausencias.index'));

        $this->assertSame('confirmado', $alocacaoOriginal->fresh()->status);
        $this->assertSame('rejeitado', $aviso->fresh()->status);
    }

    public function test_backend_bloqueia_substituto_de_cargo_diferente(): void
    {
        [$aviso] = $this->avisoPendente();
        $recepcionista = $this->recepcionista();

        $this->actingAs($this->admin())
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $recepcionista->id,
            ])
            ->assertSessionHasErrors('substituto_user_id');

        $this->assertSame('pendente', $aviso->fresh()->status);
    }

    public function test_backend_bloqueia_substituto_com_conflito_de_turno(): void
    {
        [$aviso, $alocacaoOriginal] = $this->avisoPendente();
        $substituto = $this->porteiro();

        Alocacao::factory()->create([
            'user_id' => $substituto->id,
            'data' => $alocacaoOriginal->data->toDateString(),
            'turno_id' => $alocacaoOriginal->turno_id,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $substituto->id,
            ])
            ->assertSessionHasErrors('substituto_user_id');

        $this->assertSame('pendente', $aviso->fresh()->status);
    }

    public function test_admin_nao_pode_alocar_o_proprio_solicitante(): void
    {
        [$aviso, , $solicitante] = $this->avisoPendente();

        $this->actingAs($this->admin())
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $solicitante->id,
            ])
            ->assertSessionHasErrors('substituto_user_id');
    }
}