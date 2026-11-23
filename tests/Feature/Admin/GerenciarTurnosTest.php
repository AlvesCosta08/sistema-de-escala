<?php

namespace Tests\Feature\Admin;

use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarTurnosTest extends TestCase
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

    public function test_colaborador_nao_acessa_gerenciamento_de_turnos()
    {
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $porteiro->assignRole('porteiro');

        $response = $this->actingAs($porteiro)->get(route('admin.turnos.index'));
        $response->assertStatus(403);
    }

    public function test_admin_acessa_lista_de_turnos()
    {
        $admin = $this->criarAdmin();
        Turno::create([
            'nome' => 'Turno Teste', 
            'hora_inicio' => '08:00:00', 
            'hora_fim' => '18:00:00',
            'tolerancia_atraso_min' => 10,
            'intervalo_min' => 60
        ]);

        $response = $this->actingAs($admin)->get(route('admin.turnos.index'));
        
        $response->assertOk();
        $response->assertSee('Turno Teste');
    }

    public function test_admin_cria_novo_turno()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->post(route('admin.turnos.store'), [
            'nome' => 'Turno Madrugada',
            'hora_inicio' => '23:00',
            'hora_fim' => '07:00',
            'tolerancia_atraso_min' => 15,
            'intervalo_min' => 30
        ]);

        $response->assertRedirect(route('admin.turnos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('turnos', ['nome' => 'Turno Madrugada']);
    }

    public function test_admin_edita_um_turno()
    {
        $admin = $this->criarAdmin();
        $turno = Turno::create([
            'nome' => 'Turno Antigo',
            'hora_inicio' => '08:00:00', 
            'hora_fim' => '18:00:00',
            'tolerancia_atraso_min' => 10,
            'intervalo_min' => 60
        ]);

        $response = $this->actingAs($admin)->put(route('admin.turnos.update', $turno), [
            'nome' => 'Turno Editado',
            'hora_inicio' => '09:00',
            'hora_fim' => '19:00',
            'tolerancia_atraso_min' => 20,
            'intervalo_min' => 45
        ]);

        $response->assertRedirect(route('admin.turnos.index'));
        $this->assertDatabaseHas('turnos', ['id' => $turno->id, 'nome' => 'Turno Editado']);
    }

    public function test_admin_nao_cria_turno_sem_horarios()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->post(route('admin.turnos.store'), [
            'nome' => 'Turno Incompleto',
            'hora_inicio' => '',
            'hora_fim' => '',
        ]);

        $response->assertSessionHasErrors(['hora_inicio', 'hora_fim']);
    }
}