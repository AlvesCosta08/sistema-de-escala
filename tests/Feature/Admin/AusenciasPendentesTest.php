<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AusenciasPendentesTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_admin_ve_apenas_avisos_pendentes(): void
    {
        $porteiroPendente = User::factory()->create([
            'name' => 'João Pendente',
            'cargo' => 'porteiro',
        ]);
        $porteiroResolvido = User::factory()->create([
            'name' => 'Maria Resolvida',
            'cargo' => 'porteiro',
        ]);

        $pendente = AvisoAusencia::factory()->create([
            'user_id' => $porteiroPendente->id,
            'status' => 'pendente',
        ]);
        $resolvido = AvisoAusencia::factory()->create([
            'user_id' => $porteiroResolvido->id,
            'status' => 'substituto_alocado',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.index'))
            ->assertOk()
            ->assertSee('João Pendente')
            ->assertDontSee('Maria Resolvida');
    }

    public function test_avisos_urgentes_aparecem_antes_dos_comuns(): void
    {
        $porteiroComum = User::factory()->create([
            'name' => 'Carlos Comum',
            'cargo' => 'porteiro',
        ]);
        $porteiroUrgente = User::factory()->create([
            'name' => 'Ana Urgente',
            'cargo' => 'porteiro',
        ]);

        // Comum: criado antes, mas sem urgência
        AvisoAusencia::factory()->create([
            'user_id' => $porteiroComum->id,
            'status' => 'pendente',
            'urgente' => false,
            'created_at' => now()->subHours(3),
        ]);

        // Urgente: criado depois, mas marcado urgente
        AvisoAusencia::factory()->urgente()->create([
            'user_id' => $porteiroUrgente->id,
            'status' => 'pendente',
            'created_at' => now(),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.ausencias.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ana Urgente', 'Carlos Comum']);
    }

    public function test_colaborador_nao_acessa_painel_admin(): void
    {
        $this->actingAs($this->porteiro())
            ->get(route('admin.ausencias.index'))
            ->assertForbidden();
    }
}