<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class DetalheAusenciaTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function avisoComAlocacao(array $alocacaoAttrs = [], array $avisoAttrs = []): array
    {
        $solicitante = $this->porteiro();

        $alocacao = Alocacao::factory()->create(array_merge([
            'user_id' => $solicitante->id,
            'data' => now()->addDays(2)->toDateString(),
            'status' => 'pendente',
        ], $alocacaoAttrs));

        $aviso = AvisoAusencia::factory()->create(array_merge([
            'alocacao_id' => $alocacao->id,
            'user_id' => $solicitante->id,
            'status' => 'pendente',
            'motivo' => 'atestado_medico',
            'observacao' => 'Consulta de rotina',
        ], $avisoAttrs));

        return [$aviso, $alocacao, $solicitante];
    }

    // R4 — acesso
    public function test_admin_ve_detalhe_do_aviso_com_motivo_e_observacao(): void
    {
        [$aviso] = $this->avisoComAlocacao();

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.show', $aviso))
            ->assertOk()
            ->assertSee('Atestado médico')
            ->assertSee('Consulta de rotina')
            ->assertSee($aviso->user->name);
    }

    public function test_colaborador_nao_acessa_detalhe_do_aviso(): void
    {
        [$aviso] = $this->avisoComAlocacao();

        $this->actingAs($this->porteiro())
            ->get(route('admin.ausencias.show', $aviso))
            ->assertForbidden();
    }

    // R1 — mesma função
    public function test_lista_apenas_substitutos_da_mesma_funcao(): void
    {
        [$aviso] = $this->avisoComAlocacao();

        $porteiroOk = $this->porteiro();
        $recepcionistaRuim = $this->recepcionista();

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.show', $aviso))
            ->assertOk()
            ->assertSee($porteiroOk->name)
            ->assertDontSee($recepcionistaRuim->name);
    }

    // R2 — conflito de dia + turno
    public function test_oculta_substituto_ja_alocado_no_mesmo_dia_e_turno(): void
    {
        [$aviso, $alocacao] = $this->avisoComAlocacao();

        $porteiroLivre = $this->porteiro();
        $porteiroConflitante = $this->porteiro();

        Alocacao::factory()->create([
            'user_id' => $porteiroConflitante->id,
            'data' => $alocacao->data->toDateString(),
            'turno_id' => $alocacao->turno_id,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.show', $aviso))
            ->assertOk()
            ->assertSee($porteiroLivre->name)
            ->assertDontSee($porteiroConflitante->name);
    }

    // R3 — intervalo mínimo de 11h
    public function test_oculta_substituto_que_violaria_intervalo_de_11h(): void
    {
        $turnoNoturno = Turno::factory()->noturno()->create();

        [$aviso, $alocacao] = $this->avisoComAlocacao([
            'turno_id' => $turnoNoturno->id,
            'data' => now()->addDays(2)->toDateString(),
        ]);

        $porteiroLivre = $this->porteiro();
        $porteiroEmConflito = $this->porteiro();

        $turnoDiurno = Turno::factory()->create();

        Alocacao::factory()->create([
            'user_id' => $porteiroEmConflito->id,
            'data' => $alocacao->data->toDateString(),
            'turno_id' => $turnoDiurno->id,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.show', $aviso))
            ->assertOk()
            ->assertSee($porteiroLivre->name)
            ->assertDontSee($porteiroEmConflito->name);
    }
}