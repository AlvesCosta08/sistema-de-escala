<?php

use App\Models\Posto;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('filtra a lista de postos em tempo real ao digitar no campo de busca', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    // Cria 3 postos com nomes e descrições distintos
    Posto::factory()->create(['nome' => 'Portaria Principal', 'descricao' => 'Entrada principal do condomínio']);
    Posto::factory()->create(['nome' => 'Garagem Subterrânea', 'descricao' => 'Acesso restrito nível B1']);
    Posto::factory()->create(['nome' => 'Recepção Hospitalar', 'descricao' => 'Setor de atendimento ao público']);

    $page = visit(route('admin.postos.index'));

    // 1. Inicialmente, todos os 3 postos devem estar visíveis
    $page->assertSee('Portaria Principal')
         ->assertSee('Garagem Subterrânea')
         ->assertSee('Recepção Hospitalar')
         ->assertSee('3 postos encontrados');

    // 2. Digitar "garagem" no campo de busca
    $page->type('#search', 'garagem')
         ->wait(1)
         ->assertSee('Garagem Subterrânea')
         ->assertDontSee('Portaria Principal')
         ->assertDontSee('Recepção Hospitalar')
         ->assertSee('1 posto encontrado');

    // 3. Buscar por descrição
    $page->clear('#search')
         ->type('#search', 'hospitalar')
         ->wait(1)
         ->assertSee('Recepção Hospitalar')
         ->assertDontSee('Portaria Principal')
         ->assertDontSee('Garagem Subterrânea')
         ->assertSee('1 posto encontrado');

    // 4. Buscar por algo que não existe
    $page->clear('#search')
         ->type('#search', 'xyz')
         ->wait(1)
         ->assertDontSee('Portaria Principal')
         ->assertDontSee('Garagem Subterrânea')
         ->assertDontSee('Recepção Hospitalar')
         ->assertSee('0 postos encontrados')
         ->assertSee('Nenhum posto encontrado');

    // 5. Limpar a busca e todos voltam
    $page->clear('#search')
         ->wait(1)
         ->assertSee('Portaria Principal')
         ->assertSee('Garagem Subterrânea')
         ->assertSee('Recepção Hospitalar')
         ->assertSee('3 postos encontrados');

    $page->assertNoJavaScriptErrors();
});