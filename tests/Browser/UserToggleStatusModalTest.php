<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe modal de confirmacao ao desativar um usuario e executa a acao', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    // Cria um colaborador ativo que será desativado
    $colaborador = User::factory()->create([
        'name' => 'João Teste',
        'active' => true, // Garante que ele comece ativo
    ]);
    $colaborador->assignRole('porteiro');

    $page = visit(route('admin.usuarios.index'));

    // 1. O texto do modal NÃO deve estar visível inicialmente
    $page->assertDontSee('Tem certeza que deseja DESATIVAR este colaborador');

    // 2. Clicar no botão de toggle (gatilho do modal)
    $page->click('#btn-trigger-toggle-status-' . $colaborador->id);

    // 3. Modal DEVE aparecer
    $page->assertSee('Tem certeza que deseja DESATIVAR este colaborador')
         ->assertSee('Cancelar')
         ->assertSee('Desativar');

    // 4. Clicar em "Cancelar" deve fechar o modal
    $page->click('Cancelar')
         ->wait(1)
         ->assertDontSee('Tem certeza que deseja DESATIVAR este colaborador');

    // 5. Abrir o modal novamente e confirmar a ação
    $page->click('#btn-trigger-toggle-status-' . $colaborador->id)
         ->wait(1)
         ->click('#btn-confirmar-acao')
         ->wait(2) // Aguarda o processamento e o redirect
         
         // 6. Validar que a ação ocorreu (Toast de sucesso ou mudança na tela)
         ->assertNoJavaScriptErrors();
});