<?php

use App\Models\Alocacao;
use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe modal de confirmacao ao clicar em remover alocacao e executa a acao', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $escala = Escala::factory()->create();
    $colaborador = User::factory()->create(['cargo' => 'porteiro']);
    $colaborador->assignRole('porteiro');
    $posto = Posto::factory()->create();
    $turno = Turno::factory()->create();
    
    $alocacao = Alocacao::factory()->create([
        'escala_id' => $escala->id,
        'user_id' => $colaborador->id,
        'posto_id' => $posto->id,
        'turno_id' => $turno->id,
        'data' => now()->addDay(),
    ]);

    $page = visit(route('admin.escalas.alocacoes.index', $escala));

    // 1. Modal NÃO deve estar visível inicialmente
    $page->assertDontSee('Tem certeza que deseja remover esta alocação?');

    // 2. Forçar a abertura do <details> via JavaScript
    // O script() não é encadeável, então chamamos separadamente e depois wait()
    $page->script("document.querySelector('details').setAttribute('open', 'true')");
    $page->wait(1);

    // 3. Clicar no botão "Remover" (gatilho)
    $page->click('#btn-trigger-remover-' . $alocacao->id);

    // 4. Modal DEVE aparecer
    $page->assertSee('Tem certeza que deseja remover esta alocação?')
         ->assertSee('Cancelar')
         ->assertSee('Remover');

    // 5. Clicar em "Cancelar" deve fechar o modal
    $page->click('Cancelar')
         ->wait(1)
         ->assertDontSee('Tem certeza que deseja remover esta alocação?');

    // 6. Abrir o modal novamente e confirmar a exclusão
    $page->click('#btn-trigger-remover-' . $alocacao->id)
         ->wait(1)
         ->click('#btn-confirmar-acao')
         ->wait(2)
         
         // 7. Validar que a ação ocorreu
         ->assertSee('Alocação removida com sucesso')
         ->assertNoJavaScriptErrors();
});