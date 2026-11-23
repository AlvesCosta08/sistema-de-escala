<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarUsuariosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // ESSENCIAL: Garante que as roles (admin, porteiro, etc.) existam antes de cada teste
        $this->seed(\Database\Seeders\RolesSeeder::class);
    }

    private function criarAdmin(): User
    {
        $admin = User::factory()->create([
            'name' => 'Admin Teste',
            'email' => 'admin.teste@obede.local',
            'cargo' => 'admin',
            'active' => true,
        ]);
        $admin->assignRole('admin');
        return $admin;
    }

    public function test_colaborador_nao_acessa_gerenciamento_de_usuarios()
    {
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $porteiro->assignRole('porteiro');

        $response = $this->actingAs($porteiro)->get(route('admin.usuarios.index'));

        $response->assertStatus(403);
    }

    public function test_admin_acessa_lista_de_usuarios()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->get(route('admin.usuarios.index'));

        $response->assertOk();
        $response->assertSee('Colaboradores');
    }

    public function test_admin_cria_novo_colaborador_e_atribui_role()
    {
        $admin = $this->criarAdmin();

        $dados = [
            'name' => 'Novo Porteiro',
            'email' => 'novo.porteiro@obede.local',
            'cargo' => 'porteiro',
            'password' => 'password123',
            'active' => 1,
        ];

        $response = $this->actingAs($admin)->post(route('admin.usuarios.store'), $dados);

        $response->assertRedirect(route('admin.usuarios.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'novo.porteiro@obede.local',
            'cargo' => 'porteiro',
            'active' => true,
        ]);

        $novoUser = User::where('email', 'novo.porteiro@obede.local')->first();
        $this->assertTrue($novoUser->hasRole('porteiro'));
    }

    public function test_admin_nao_pode_criar_usuario_com_email_duplicado()
    {
        $admin = $this->criarAdmin();
        
        User::factory()->create(['email' => 'duplicado@obede.local']);

        $dados = [
            'name' => 'Outro Cara',
            'email' => 'duplicado@obede.local',
            'cargo' => 'recepcionista',
            'password' => 'password123',
        ];

        $response = $this->actingAs($admin)->post(route('admin.usuarios.store'), $dados);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::where('email', 'duplicado@obede.local')->count());
    }

    public function test_admin_pode_alternar_status_do_colaborador()
    {
        $admin = $this->criarAdmin();
        
        $colaborador = User::factory()->create(['active' => true]);
        $colaborador->assignRole('porteiro');

        // Desativar
        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $colaborador));
        $response->assertSessionHas('success');
        $this->assertFalse($colaborador->fresh()->active);

        // Reativar
        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $colaborador));
        $this->assertTrue($colaborador->fresh()->active);
    }

    public function test_admin_nao_pode_desativar_a_si_mesmo()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->patch(route('admin.usuarios.toggle', $admin));

        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->active);
    }
}