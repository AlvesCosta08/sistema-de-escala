<?php

namespace Tests\Feature\Admin;

use App\Models\Posto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarPostosTest extends TestCase
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

    public function test_colaborador_nao_acessa_gerenciamento_de_postos()
    {
        $porteiro = User::factory()->create(['cargo' => 'porteiro']);
        $porteiro->assignRole('porteiro');

        $response = $this->actingAs($porteiro)->get(route('admin.postos.index'));
        $response->assertStatus(403);
    }

    public function test_admin_acessa_lista_de_postos()
    {
        $admin = $this->criarAdmin();
        Posto::create(['nome' => 'Portaria Teste', 'descricao' => 'Teste']);

        $response = $this->actingAs($admin)->get(route('admin.postos.index'));
        
        $response->assertOk();
        $response->assertSee('Portaria Teste');
    }

    public function test_admin_cria_novo_posto()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->post(route('admin.postos.store'), [
            'nome' => 'Novo Posto',
            'descricao' => 'Descrição do novo posto'
        ]);

        $response->assertRedirect(route('admin.postos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('postos', ['nome' => 'Novo Posto']);
    }

    public function test_admin_edita_um_posto()
    {
        $admin = $this->criarAdmin();
        $posto = Posto::create(['nome' => 'Posto Antigo']);

        $response = $this->actingAs($admin)->put(route('admin.postos.update', $posto), [
            'nome' => 'Posto Editado',
            'descricao' => 'Nova descrição'
        ]);

        $response->assertRedirect(route('admin.postos.index'));
        $this->assertDatabaseHas('postos', ['id' => $posto->id, 'nome' => 'Posto Editado']);
    }

    public function test_admin_nao_cria_posto_sem_nome()
    {
        $admin = $this->criarAdmin();

        $response = $this->actingAs($admin)->post(route('admin.postos.store'), [
            'nome' => '',
        ]);

        $response->assertSessionHasErrors('nome');
    }
}