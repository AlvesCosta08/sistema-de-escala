<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class CalendarioAdminTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_admin_acessa_calendario_do_mes_atual(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.escalas.index'))
            ->assertOk();
    }

    public function test_colaborador_nao_acessa_calendario_admin(): void
    {
        $this->actingAs($this->porteiro())
            ->get(route('admin.escalas.index'))
            ->assertForbidden();
    }

    public function test_calendario_exibe_foto_e_nome_dos_alocados(): void
    {
        $porteiro = User::factory()->create([
            'name' => 'João Silva',
            'cargo' => 'porteiro',
            'foto_thumb_path' => 'fotos/thumbs/joao.webp',
        ]);

        Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'data' => now()->startOfMonth()->addDays(4)->toDateString(),
            'status' => 'confirmado',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.escalas.index'))
            ->assertOk()
            ->assertSee('João Silva')
            ->assertSee('fotos/thumbs/joao.webp');
    }

    public function test_calendario_nao_mostra_alocacoes_de_outro_mes(): void
    {
        $porteiro = User::factory()->create([
            'name' => 'Maria Outro Mês',
            'cargo' => 'porteiro',
        ]);

        Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'data' => now()->addMonth()->startOfMonth()->addDays(2)->toDateString(),
            'status' => 'confirmado',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.escalas.index', ['mes' => now()->format('Y-m')]))
            ->assertOk()
            ->assertDontSee('Maria Outro Mês');
    }

    public function test_calendario_exibe_inicial_quando_sem_foto(): void
    {
        $porteiro = User::factory()->create([
            'name' => 'Carlos Sem Foto',
            'cargo' => 'porteiro',
            'foto_thumb_path' => null,
        ]);

        Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'data' => now()->startOfMonth()->addDays(3)->toDateString(),
            'status' => 'confirmado',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.escalas.index'))
            ->assertOk()
            ->assertSee('Carlos Sem Foto');
    }
}