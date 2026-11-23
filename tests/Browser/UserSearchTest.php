<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('filtra a lista de usuarios em tempo real ao digitar no campo de busca', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $joao = User::factory()->create(['name' => 'João Silva', 'email' => 'joao@obede.com']);
    $joao->assignRole('porteiro');

    $maria = User::factory()->create(['name' => 'Maria Santos', 'email' => 'maria@obede.com']);
    $maria->assignRole('recepcionista');

    $pedro = User::factory()->create(['name' => 'Pedro Almeida', 'email' => 'pedro@obede.com']);
    $pedro->assignRole('porteiro');

    $page = visit(route('admin.usuarios.index'));

    // 1. Inicialmente, todos os 4 colaboradores (incluindo o admin) devem estar visíveis
    $page->assertSee('João Silva')
         ->assertSee('Maria Santos')
         ->assertSee('Pedro Almeida')
         ->assertSee('4 colaboradores encontrados');

    // 2. Digitar "jo" no campo de busca (usando seletor CSS #search)
    $page->type('#search', 'jo')
         ->wait(1)
         ->assertSee('João Silva')
         ->assertDontSee('Maria Santos')
         ->assertDontSee('Pedro Almeida')
         ->assertSee('1 colaborador encontrado');

    // 3. Buscar por email
    $page->clear('#search')
         ->type('#search', 'maria@')
         ->wait(1)
         ->assertSee('Maria Santos')
         ->assertDontSee('João Silva')
         ->assertDontSee('Pedro Almeida')
         ->assertSee('1 colaborador encontrado');

    // 4. Buscar por algo que não existe
    $page->clear('#search')
         ->type('#search', 'xyz')
         ->wait(1)
         ->assertDontSee('João Silva')
         ->assertDontSee('Maria Santos')
         ->assertDontSee('Pedro Almeida')
         ->assertSee('0 colaboradores encontrados')
         ->assertSee('Nenhum colaborador encontrado');

    // 5. Limpar a busca e todos voltam
    $page->clear('#search')
         ->wait(1)
         ->assertSee('João Silva')
         ->assertSee('Maria Santos')
         ->assertSee('Pedro Almeida')
         ->assertSee('4 colaboradores encontrados');

    $page->assertNoJavaScriptErrors();
});